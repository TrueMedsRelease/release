<?php

namespace Tests\Unit;

use App\Http\Controllers\AccountController;
use App\Services\CabinetService;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class CabinetServiceTest extends TestCase
{
    public function test_pkce_pair_format(): void
    {
        $pair = CabinetService::pkcePair();

        $this->assertArrayHasKey('verifier', $pair);
        $this->assertArrayHasKey('challenge', $pair);
        $this->assertSame('S256', $pair['method']);

        // verifier: 64 случайных байта в base64url без паддинга.
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9\-_]{86}$/', $pair['verifier']);
        // challenge: BASE64URL(SHA256(verifier)) без паддинга.
        $expected = rtrim(strtr(base64_encode(hash('sha256', $pair['verifier'], true)), '+/', '-_'), '=');
        $this->assertSame($expected, $pair['challenge']);
    }

    public function test_pkce_pairs_are_unique(): void
    {
        $this->assertNotSame(CabinetService::pkcePair()['verifier'], CabinetService::pkcePair()['verifier']);
    }

    public function test_login_url(): void
    {
        $url = CabinetService::loginUrl('https://true-help.com/', 'shop-example.com', 'state123', 'challengeABC');

        $this->assertSame(
            'https://true-help.com/login?s=shop-example.com&state=state123&code_challenge=challengeABC&code_challenge_method=S256',
            $url
        );
    }

    public function test_login_url_passes_design(): void
    {
        $url = CabinetService::loginUrl('https://true-help.com/', 'shop-example.com', 'state123', 'challengeABC', 'S256', 'design_4');

        $this->assertSame(
            'https://true-help.com/login?s=shop-example.com&state=state123&code_challenge=challengeABC&code_challenge_method=S256&design=design_4',
            $url
        );
    }

    public function test_login_url_drops_invalid_design(): void
    {
        foreach (['design_x', '../etc', 'design_4;">', '', '14'] as $bad) {
            $url = CabinetService::loginUrl('https://true-help.com/', 'shop-example.com', 'state123', 'challengeABC', 'S256', $bad);

            $this->assertStringNotContainsString('design=', $url);
        }
    }

    public function test_login_url_passes_lang(): void
    {
        $url = CabinetService::loginUrl('https://true-help.com/', 'shop-example.com', 'state123', 'challengeABC', 'S256', 'design_4', 'de');

        $this->assertSame(
            'https://true-help.com/login?s=shop-example.com&state=state123&code_challenge=challengeABC&code_challenge_method=S256&design=design_4&lang=de',
            $url
        );
    }

    public function test_login_url_drops_invalid_lang(): void
    {
        foreach (['xx', '../etc', 'de;">', '', 'design_4'] as $bad) {
            $url = CabinetService::loginUrl('https://true-help.com/', 'shop-example.com', 'state123', 'challengeABC', 'S256', 'design_4', $bad);

            $this->assertStringNotContainsString('lang=', $url);
        }
    }

    public function test_interpret_test_connection_ok(): void
    {
        $result = CabinetService::interpretTestConnection(401, ['error' => 'Invalid or expired code.']);

        $this->assertSame('ok', $result['status']);
    }

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

    public function test_random_base64_url_format(): void
    {
        $value = CabinetService::randomBase64Url(24);

        // 24 байта в base64url без паддинга = 32 символа.
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9\-_]{32}$/', $value);
        $this->assertNotSame($value, CabinetService::randomBase64Url(24));
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
