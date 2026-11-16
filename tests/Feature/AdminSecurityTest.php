<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    private string $envBackup;

    protected function setUp(): void
    {
        parent::setUp();
        // These tests exercise code that writes .env — always restore it,
        // even if a check regresses and something gets written.
        $this->envBackup = file_get_contents(base_path('.env'));
        RateLimiter::clear(sha1('127.0.0.1'));
        RateLimiter::clear('admin-login-fails:127.0.0.1');
        // APP_URL may be a bare host (no scheme); pin a clean root.
        URL::forceRootUrl('http://localhost');
    }

    protected function tearDown(): void
    {
        file_put_contents(base_path('.env'), $this->envBackup);
        parent::tearDown();
    }

    public function test_login_without_solved_captcha_is_rejected(): void
    {
        $response = $this->post('/admin/request_login', [
            'password' => 'anything',
        ]);

        $response->assertOk();
        $this->assertSame('error', $response->json('status'));
        $this->assertNotTrue(session('logged_in'));
    }

    public function test_solved_captcha_mark_is_single_use(): void
    {
        $this->withSession(['captcha_passed_at' => time()])
            ->post('/admin/request_login', ['password' => '']);

        $this->assertNull(session('captcha_passed_at'));
    }

    public function test_expired_captcha_mark_is_rejected(): void
    {
        $response = $this->withSession(['captcha_passed_at' => time() - 600])
            ->post('/admin/request_login', ['password' => 'anything']);

        $this->assertSame('error', $response->json('status'));
    }

    private function attemptLogin(string $password)
    {
        return $this->withSession(['captcha_passed_at' => time()])
            ->post('/admin/request_login', ['password' => $password]);
    }

    private function fakePassword(string $valid): void
    {
        $this->partialMock(AdminController::class, function ($mock) use ($valid) {
            $mock->shouldReceive('passwordMatches')
                ->andReturnUsing(fn (string $p) => $p === $valid);
        });
    }

    public function test_locks_after_five_wrong_passwords(): void
    {
        $this->fakePassword('right');

        for ($i = 0; $i < 5; $i++) {
            $this->assertSame('error', $this->attemptLogin('wrong')->json('status'));
        }

        // Even the correct password is refused while locked.
        $response = $this->attemptLogin('right');
        $this->assertSame('error', $response->json('status'));
        $this->assertStringContainsString('Too many', $response->json('text'));
        $this->assertNotTrue(session('logged_in'));
    }

    public function test_successful_login_resets_counter(): void
    {
        $this->fakePassword('right');

        for ($i = 0; $i < 4; $i++) {
            $this->attemptLogin('wrong');
        }
        $this->assertSame('success', $this->attemptLogin('right')->json('status'));

        // Counter is back to zero: 4 more wrong attempts still don't lock.
        for ($i = 0; $i < 4; $i++) {
            $this->attemptLogin('wrong');
        }
        $this->assertSame('success', $this->attemptLogin('right')->json('status'));
    }

    public function test_missing_captcha_does_not_count_as_failed_password(): void
    {
        $this->fakePassword('right');

        for ($i = 0; $i < 10; $i++) {
            $this->post('/admin/request_login', ['password' => 'wrong']);
        }

        $this->assertSame('success', $this->attemptLogin('right')->json('status'));
    }

    public function test_route_has_flood_limit(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->post('/admin/request_login', ['password' => 'x'])->assertOk();
        }

        $this->post('/admin/request_login', ['password' => 'x'])->assertStatus(429);
    }

    /**
     * @dataProvider badTemplates
     */
    public function test_save_template_rejects_invalid_names(string $template): void
    {
        $before = file_get_contents(base_path('.env'));

        $response = $this->withSession(['logged_in' => true])
            ->post('/admin/main_properties/save_template', ['selected_template' => $template]);

        $response->assertOk();
        $this->assertSame('error', $response->json('status'));
        $this->assertSame($before, file_get_contents(base_path('.env')));
    }

    public static function badTemplates(): array
    {
        return [
            'newline injection' => ["design_14\nADMIN_AUTOLOGIN_TOKEN=abc"],
            'non-existent'      => ['design_999'],
            'non-design dir'    => ['admin'],
            'traversal'         => ['../design_14'],
            'empty'             => [''],
        ];
    }

    /**
     * @dataProvider badEnvValues
     */
    public function test_set_env_rejects_unsafe_values(string $value): void
    {
        $before = file_get_contents(base_path('.env'));

        try {
            (new AdminController())->setEnvMany(['APP_CURRENCY' => $value]);
            $this->fail('Expected InvalidArgumentException');
        } catch (\InvalidArgumentException) {
            // expected
        }

        $this->assertSame($before, file_get_contents(base_path('.env')));
    }

    public static function badEnvValues(): array
    {
        return [
            'newline'     => ["usd\nAPP_DEBUG=true"],
            'cr'          => ["usd\rAPP_DEBUG=true"],
            'expansion'   => ['${DB_PASSWORD}'],
            'quote'       => ['"usd'],
            'comment'     => ['usd #x'],
        ];
    }
}
