<?php

namespace Tests\Unit;

use App\Http\Controllers\AccountController;
use App\Services\CabinetService;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class CabinetServiceTest extends TestCase
{
    public function test_interpret_test_connection_not_registered(): void
    {
        $result = CabinetService::interpretTestConnection(401, ['error' => 'Unknown shop or bad secret.']);

        $this->assertSame('not_registered', $result['status']);
    }

    public function test_interpret_test_connection_rate_limited(): void
    {
        $result = CabinetService::interpretTestConnection(429, ['error' => 'Too many requests. Please try again later.']);

        $this->assertSame('fail', $result['status']);
    }

    public function test_interpret_test_connection_unexpected(): void
    {
        $result = CabinetService::interpretTestConnection(500, []);

        $this->assertSame('fail', $result['status']);
    }

    public function test_scope_for_path(): void
    {
        $this->assertSame('me', CabinetService::scopeForPath('me'));
        $this->assertSame('orders', CabinetService::scopeForPath('orders'));
        $this->assertSame('orders.shipping', CabinetService::scopeForPath('orders/12/shipping'));
        $this->assertSame('orders.tracking', CabinetService::scopeForPath('orders/12/tracking'));
        $this->assertSame('orders.stop_recurring', CabinetService::scopeForPath('orders/12/stop-recurring'));
        $this->assertSame('orders', CabinetService::scopeForPath('orders/12/repeat-virtual'));
        $this->assertSame('orders', CabinetService::scopeForPath('orders/12/repeat-context'));
        $this->assertSame('bonus', CabinetService::scopeForPath('bonus/program'));
        $this->assertSame('referral', CabinetService::scopeForPath('referral'));
        $this->assertSame('sessions', CabinetService::scopeForPath('sessions'));
        $this->assertSame('sessions.revoke', CabinetService::scopeForPath('sessions/revoke-all'));
        $this->assertSame('contact', CabinetService::scopeForPath('contact'));
        $this->assertSame('chat', CabinetService::scopeForPath('chat/messages'));
        $this->assertSame('chat', CabinetService::scopeForPath('chat/messages', 'GET'));
        $this->assertSame('chat.send', CabinetService::scopeForPath('chat/messages', 'POST'));
    }

    public function test_scope_for_path_unknown(): void
    {
        $this->assertNull(CabinetService::scopeForPath('unknown'));
        $this->assertNull(CabinetService::scopeForPath('orders/abc/shipping'));
        $this->assertNull(CabinetService::scopeForPath(''));
    }

    public function test_browser_fingerprint_empty_is_null(): void
    {
        $this->assertNull($this->fingerprint(new Request()));
        $this->assertNull($this->fingerprint(new Request(['fingerprint' => ''])));
        $this->assertNull($this->fingerprint(new Request(['fingerprint' => '   '])));
    }

    public function test_browser_fingerprint_trimmed_and_truncated(): void
    {
        $this->assertSame('fp-abc', $this->fingerprint(new Request(['fingerprint' => '  fp-abc  '])));

        $long = str_repeat('x', 200);
        $result = $this->fingerprint(new Request(['fingerprint' => $long]));
        $this->assertSame(128, strlen((string) $result));
        $this->assertSame(substr($long, 0, 128), $result);
    }

    private function fingerprint(Request $request): ?string
    {
        $method = new \ReflectionMethod(AccountController::class, 'browserFingerprint');

        return $method->invoke(new AccountController(), $request);
    }
}
