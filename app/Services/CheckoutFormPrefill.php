<?php

namespace App\Services;

/**
 * Предзаполнение формы чекаута (ключи session('form.*')) данными
 * из адреса доставки заказа кабинета (GET /api/v1/orders/{id}/shipping).
 *
 * Используется повтором заказа из "My orders": товары кладутся в корзину
 * шопа, а этим маппингом форма сразу заполняется данными, с которыми
 * был оформлен исходный заказ. Только непустые значения — пустыми
 * ничего не затираем. Платёжные поля (карта/купоны) не трогаем.
 */
class CheckoutFormPrefill
{
    /**
     * @param array<string, mixed> $shipping firstname, lastname, countryCode,
     *     state, city, address, postalCode (формат ShippingInfo кабинета)
     */
    public static function fromShipping(array $shipping, ?string $email = null): array
    {
        $str = static fn(string $key): string => trim((string) ($shipping[$key] ?? ''));
        $code = static fn(string $key): string => strtoupper(trim((string) ($shipping[$key] ?? '')));

        $form = [];

        $email = trim((string) ($email ?? ''));
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $form['email'] = $email;
        }

        foreach (['firstname', 'lastname'] as $key) {
            $value = $str($key);
            if ($value !== '') {
                $form[$key] = $value;
            }
        }

        $country = $code('countryCode');
        if ($country === '') {
            $country = $code('country_iso2');
        }
        $state = $code('state');
        if ($state === '') {
            $state = $code('state_code');
        }
        $zip = $str('postalCode');
        if ($zip === '') {
            $zip = $str('postcode');
        }

        if ($country !== '') {
            $form['billing_country'] = $country;
            $form['shipping_country'] = $country;
            // Селект телефонного кода матчится по iso страны.
            $form['phone_code'] = $country;
        }
        if ($state !== '') {
            $form['billing_state'] = $state;
            $form['shipping_state'] = $state;
        }
        if ($zip !== '') {
            $form['billing_zip'] = $zip;
            $form['shipping_zip'] = $zip;
        }
        foreach (['billing_city', 'billing_address', 'shipping_city', 'shipping_address'] as $key) {
            $src = str_replace(['billing_', 'shipping_'], '', $key);
            $value = $str($src);
            if ($value !== '') {
                $form[$key] = $value;
            }
        }

        return $form;
    }
}
