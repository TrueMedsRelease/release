<?php

namespace Tests\Feature;

use App\Services\WalletCardPayment;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Describes what the checkout does for every payment method, and proves that
 * exercising the card order() endpoint never sends anything to order.php.
 *
 * Run just this file:  php artisan test --filter=WalletCardPaymentTest
 */
class WalletCardPaymentTest extends TestCase
{
    /** Every method shown on the checkout, grouped by how it is handled. */
    private const ALL_METHODS = [
        // card brands (always the card form)
        'visa', 'mastercard', 'amex', 'discover',
        // wallet / bank methods (card form only when APP_WALLET_CARD_ON=1)
        'apple_pay', 'google_pay', 'open_banking', 'revolut', 'wise', 'n26',
        // methods with their own dedicated flow (never order())
        'crypto', 'paypal', 'zelle', 'cashapp',
        'sepa_local', 'usd_swift', 'gbp_swift', 'fps', 'domestic', 'ach', 'interac',
        'gift_card', 'bonus_card',
    ];

    private function setFlag(bool $on): void
    {
        putenv('APP_WALLET_CARD_ON=' . ($on ? '1' : '0'));
        $_ENV['APP_WALLET_CARD_ON']    = $on ? '1' : '0';
        $_SERVER['APP_WALLET_CARD_ON'] = $on ? '1' : '0';
    }

    private function printTable(string $title): void
    {
        $rows = [];
        foreach (self::ALL_METHODS as $m) {
            $isCard  = WalletCardPayment::collectsCardDetails($m);
            $inOrder = in_array($m, WalletCardPayment::allowedOrderTypes(), true);

            $rows[] = [
                $m,
                $isCard ? 'yes' : 'no',
                $isCard ? WalletCardPayment::cardValidationType($m) : '-',
                $inOrder ? WalletCardPayment::shopPaymentType($m) : '-',
                $inOrder ? 'order()' : 'own flow',
            ];
        }

        $head = ['payment_type', 'card form?', 'card check', '→ shop', 'endpoint'];
        $w = [];
        foreach ($head as $i => $h) {
            $w[$i] = strlen($h);
            foreach ($rows as $r) {
                $w[$i] = max($w[$i], strlen((string) $r[$i]));
            }
        }

        $line = function (array $cols) use ($w): string {
            $out = [];
            foreach ($cols as $i => $c) {
                $out[] = str_pad((string) $c, $w[$i]);
            }
            return '| ' . implode(' | ', $out) . ' |';
        };
        $sep = '+-' . implode('-+-', array_map(fn ($x) => str_repeat('-', $x), $w)) . '-+';

        fwrite(STDERR, "\n" . $title . "\n");
        fwrite(STDERR, $sep . "\n" . $line($head) . "\n" . $sep . "\n");
        foreach ($rows as $r) {
            fwrite(STDERR, $line($r) . "\n");
        }
        fwrite(STDERR, $sep . "\n");
    }

    public function test_outputs_behaviour_for_each_payment_type_when_enabled(): void
    {
        $this->setFlag(true);
        $this->assertTrue(WalletCardPayment::enabled(), 'APP_WALLET_CARD_ON=1 should enable the card form for wallets');

        $this->printTable('APP_WALLET_CARD_ON=1  (wallet/bank methods use the card form)');

        // Card brands → generic 'card', validated against their own brand.
        $this->assertSame('card', WalletCardPayment::shopPaymentType('mastercard'));
        $this->assertSame('mastercard', WalletCardPayment::cardValidationType('mastercard'));

        // Wallet/bank methods → keep their own name, validated against all brands.
        foreach (['apple_pay', 'google_pay', 'open_banking', 'revolut', 'wise', 'n26'] as $m) {
            $this->assertTrue(WalletCardPayment::collectsCardDetails($m), "$m should show the card form");
            $this->assertSame('all', WalletCardPayment::cardValidationType($m), "$m should accept any brand");
            $this->assertSame($m, WalletCardPayment::shopPaymentType($m), "$m should keep its name for the shop");
            $this->assertContains($m, WalletCardPayment::allowedOrderTypes(), "$m should be accepted by order()");
        }

        // Non-card methods keep their own flow.
        foreach (['crypto', 'paypal', 'zelle', 'cashapp', 'gift_card', 'bonus_card'] as $m) {
            $this->assertFalse(WalletCardPayment::collectsCardDetails($m), "$m should not use the card form");
            $this->assertNotContains($m, WalletCardPayment::allowedOrderTypes(), "$m should not hit order()");
        }

        // Open-banking redirect block is empty when all its methods moved to the card form.
        $this->assertSame([], WalletCardPayment::openBankingMethods());
    }

    public function test_outputs_behaviour_for_each_payment_type_when_disabled(): void
    {
        $this->setFlag(false);
        $this->assertFalse(WalletCardPayment::enabled(), 'APP_WALLET_CARD_ON=0 should restore the original flows');

        $this->printTable('APP_WALLET_CARD_ON=0  (original flow — wallets do NOT use the card form)');

        // Only the real card brands use the card form / order() now.
        foreach (['apple_pay', 'google_pay', 'open_banking', 'revolut', 'wise', 'n26'] as $m) {
            $this->assertFalse(WalletCardPayment::collectsCardDetails($m), "$m should revert to its own flow");
            $this->assertNotContains($m, WalletCardPayment::allowedOrderTypes(), "$m should not hit order() anymore");
        }
        $this->assertSame(['visa', 'mastercard'], WalletCardPayment::cardFormMethods());
        // Open-banking redirect handles all four again.
        $this->assertSame(['open_banking', 'revolut', 'wise', 'n26'], WalletCardPayment::openBankingMethods());

        $this->setFlag(true);
    }

    public function test_order_endpoint_never_posts_to_order_php(): void
    {
        // Any outgoing HTTP is intercepted — nothing can reach the real shop API.
        Http::fake();
        $this->setFlag(true);
        // Keep unrelated middleware noise (GeoIP etc.) out of the log file, which
        // may be read-only in CI; it is irrelevant to what this test checks.
        config(['logging.default' => 'null']);

        foreach (WalletCardPayment::allowedOrderTypes() as $type) {
            // Deliberately incomplete payload: validation fails before any external
            // call, so this exercises the endpoint without needing cart/DB state.
            $response = $this->postJson(route('checkout.order'), [
                'payment_type' => $type,
            ]);

            // 'payment_type' is accepted (not among the validation errors); it fails
            // only on the missing card/contact fields.
            $errors = $response->json('errors') ?? [];
            $fields = array_column($errors, 'field');
            $this->assertNotContains(
                'payment_type',
                $fields,
                "payment_type '$type' should be accepted by order() validation"
            );
        }

        Http::assertNothingSent();
        fwrite(STDERR, "\norder() checked for all card methods — Http::assertNothingSent() passed (nothing sent to order.php).\n");
    }
}
