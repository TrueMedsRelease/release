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

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
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
