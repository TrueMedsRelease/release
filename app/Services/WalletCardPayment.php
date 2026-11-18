<?php

namespace App\Services;

/**
 * Single source of truth for how checkout payment methods behave.
 *
 * "Card brands" are entered on the card form and reported to the shop as a
 * generic 'card'. The "wallet/bank methods" (Apple Pay, Google Pay, Open
 * Banking, Revolut, Wise, N26) optionally reuse that same card form but keep
 * their own name when sent to the shop.
 *
 * The whole wallet-as-card behaviour is toggled by the APP_WALLET_CARD_ON env
 * flag: set it to 0 to fully restore the original wallet/open-banking flows.
 */
class WalletCardPayment
{
    /** Card brands the shop reports generically as 'card'. */
    public const CARD_BRANDS = ['visa', 'mastercard', 'amex', 'discover'];

    /** Wallet/bank methods that collect card details when the feature is on. */
    public const WALLET_METHODS = ['apple_pay', 'google_pay', 'open_banking', 'revolut', 'wise', 'n26'];

    /** Brands shown on the card form by default (independently of the flag). */
    public const CARD_FORM_BRANDS = ['visa', 'mastercard'];

    /** Methods originally handled by the open-banking redirect block. */
    public const OPEN_BANKING_METHODS = ['open_banking', 'revolut', 'wise', 'n26'];

    /** Whether wallet/bank methods currently collect card details. */
    public static function enabled(): bool
    {
        return (bool) env('APP_WALLET_CARD_ON', 1);
    }

    /** Wallet/bank methods that currently use the card form (empty when disabled). */
    public static function walletCardMethods(): array
    {
        return self::enabled() ? self::WALLET_METHODS : [];
    }

    /** All methods that show the card-number form. */
    public static function cardFormMethods(): array
    {
        return array_merge(self::CARD_FORM_BRANDS, self::walletCardMethods());
    }

    /** Methods still handled by the open-banking redirect block. */
    public static function openBankingMethods(): array
    {
        return array_values(array_diff(self::OPEN_BANKING_METHODS, self::walletCardMethods()));
    }

    /** Payment types accepted by the card order() endpoint. */
    public static function allowedOrderTypes(): array
    {
        return array_merge(self::CARD_BRANDS, self::walletCardMethods());
    }

    /** Does this method collect card details (and therefore hit order())? */
    public static function collectsCardDetails(string $type): bool
    {
        return in_array($type, self::cardFormMethods(), true);
    }

    /** Card brand to validate the number against; 'all' for non-brand methods. */
    public static function cardValidationType(string $type): string
    {
        return in_array($type, self::CARD_BRANDS, true) ? $type : 'all';
    }

    /** The payment_type value reported to the shop for a given method. */
    public static function shopPaymentType(string $type): string
    {
        return in_array($type, self::CARD_BRANDS, true) ? 'card' : $type;
    }
}
