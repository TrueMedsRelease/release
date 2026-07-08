<?php

namespace App\Http\Controllers;

use App\Helpers\SessionHelper;
use App\Models\Cart;
use App\Models\CountryInfoCache;
use App\Models\Currency;
use App\Models\Language;
use App\Models\PhoneCodes;
use App\Models\ProductTypeDesc;
use App\Models\State;
use App\Services\CacheServices;
use App\Services\ProductServices;
use App\Services\StatisticService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Phattarachai\LaravelMobileDetect\Agent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use App\Helpers\RequestHelper;

class CheckoutController extends Controller
{
    public function index()
    {
        if (Cookie::has('success_order_page')) {
            return redirect()->route('checkout.complete');
        }

        if (empty(session('cart')) || !session()->has('cart')) {
            return redirect(route('home.index'));
        }

        if (session('crypto')) {
            Session::forget('crypto');
        }

        // $statisticPromise = StatisticService::SendStatistic('checkout');
        StatisticService::SendCheckout();

        $this->ensureRequiredShopKeys();
        $this->ensureOrderCacheRetryColumns();
        $this->retryUnsentOrders();

        $pixels = DB::table('pixel')->where('page', '=', 'checkout')->get();
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $design = session('design') ? session('design') : config('app.design');

        // if (!is_null($statisticPromise)) {
        //     $statisticPromise->wait();
        // }

        $paypal_limit = 'none';
        $api_key      = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $message = [
            'method'  => 'get_paypal_limit',
            'api_key' => $api_key->key_data,
        ];

        if (env("APP_PAYPAL_ON", false) && checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(10)->post('http://true-serv.net/checkout/order.php', $message);
                Log::info("Paypal limit answer: " . $response);
                $response = json_decode($response, true);

                if ($response['status'] == 'success') {
                    $paypal_limit = $response['limit'];
                    session(['paypal_limit' => $paypal_limit]);
                } else {
                    session(['paypal_limit' => $paypal_limit]);
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }

        // if (session('location.country') == "US") {
        //     session(['form.payment_type' => 'zelle']);
        // }

        $agent = new Agent();

        if ($agent->isAndroidOS()) {
            $device = 'android';
        } elseif ($agent->is('iPhone') || $agent->is('iPad') || $agent->is('iPod')) {
            $device = 'apple';
        } elseif ($agent->isDesktop()) {
            $device = 'desktop';
        } else {
            $device = 'unknown';
        }

        session(['device' => $device]);

        if ($design == 'design_17') {
            $codes = HomeController::getAllCountryISO();
            $language_id = isset(Language::$languages[App::currentLocale()]) ? Language::$languages[App::currentLocale()] : Language::$languages['en'];

            foreach ($codes as $i => $code) {
                $codes[$i] = strtolower($code->iso);
            }

            $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
            $last_char = strlen($domain) - 1;
            if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
                $domain = substr($domain, 0, -1);
            }

            $web_statistic["params_string"] =
                "aff=" . session('aff', 0) .
                "&saff=" . session('saff', '') .
                "&is_uniq=" . session('uniq', 0) .
                "&keyword=" . session('keyword', '') .
                "&ref=" . session('referer', '') .
                "&domain_from=" . parse_url(config('app.url'), PHP_URL_HOST) .
                "&store_skin=" . str_replace('design_', '', $design) .
                "&page=main&device=" . $device .
                "&timestamp=" . time() .
                "&user_ip=" . RequestHelper::GetUserIp();

            return view($design . '.checkout', [
                'pixel'    => $pixel,
                'Language' => Language::class,
                'Currency' => Currency::class,
                'design' => $design,
                'codes' => json_encode($codes),
                'domain' => $domain,
                'web_statistic' => $web_statistic,

            ]);
        } else {
            return view('checkout', [
                'pixel'    => $pixel,
                'Language' => Language::class,
                'Currency' => Currency::class,
            ]);
        }
    }

    public function checkout()
    {
        if (empty(session('cart')) || !session()->has('cart')) {
            return redirect(route('home.index'));
        }

        $language_id = isset(Language::$languages[App::currentLocale()]) ? Language::$languages[App::currentLocale()] : Language::$languages['en'];
        $design      = session('design') ? session('design') : config('app.design');
        $desc        = ProductServices::GetProductDesc($language_id);
        $products    = session('cart', []);

        CacheServices::CheckCountryInfo();

        $types = ProductTypeDesc::query()
            ->where('language_id', '=', $language_id)
            ->get(['type_id', 'name']);

        $product_total_check = 0;
        foreach ($products as $value) {
            if ($value['product_id'] == 616) {
                continue;
            }
            $product_total_check += $value['price'] * $value['q'];
        }

        $product_total = 0;
        $card_only     = true;
        foreach ($products as &$item) {
            $item['name']      = $desc[$item['product_id']]['name'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            if ($item['dosage'] != '1card') {
                if (in_array($item['product_id'], [619, 620, 483, 484, 501, 615])) {
                    $item['pack_name'] = $item['name'];
                } else {
                    $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                }
                $card_only = false;
            } else {
                $item['pack_name'] = $item['name'];
            }
            $product_total += $item['price'] * $item['q'];
        }
        unset($item);

        $product_total       += session('cart_option.bonus_price');
        $product_total_check += session('cart_option.bonus_price');

        $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', session('form.billing_country', session('location.country')))
            ->get()
            ->toArray();

        $country_info = $country_info[0];
        $shipping     = json_decode($country_info['info'], true);

        if (session('aff') == 1051) {
            $shipping['regular'] = 12.99;
        }

        $cart_option = session('cart_option', [
            'shipping' => env('APP_DEFAULT_SHIPPING')
        ]);

        if (!isset($cart_option['shipping'])) {
            $cart_option['shipping'] = env('APP_DEFAULT_SHIPPING');
        }

        $cart_option['insurance_price'] = Cart::CalcInsurance();
        $cart_option['secret_price']    = $shipping['secret_package'];

        if ($cart_option['shipping'] == 'regular' && $product_total_check >= 200) {
            $cart_option['shipping_price'] = 0;
        } elseif ($cart_option['shipping'] == 'ems' && $product_total_check >= 300) {
            $cart_option['shipping_price'] = 0;
        } else {
            $cart_option['shipping_price'] = $shipping[$cart_option['shipping']];
        }

        session(['cart_option' => $cart_option]);

        if (session('cart_option.bonus_id') != 0) {
            $bonus = ProductServices::GetBonuses(session('cart_option.bonus_id'));
        } else {
            $bonus = '';
        }

        $phone_codes = PhoneCodes::all()->toArray();
        $countries   = CountryInfoCache::all()->toArray();

        Cart::update_cart_total();

        if (!empty(session('form.phone')) && str_contains(session('form.phone'), '+')) {
            for ($i = 0; $i < count($phone_codes); $i++) {
                if ($phone_codes[$i]['iso'] == session('form.billing_country')) {
                    Session::put('form.phone_code', $phone_codes[$i]['iso']);
                    $code = '+' . $phone_codes[$i]['phonecode'];
                    Session::put('form.phone', str_replace($code, '', session('form.phone')));
                }
            }
        }

        $service_enable = true;
        if (!checkdnsrr('true-serv.net', 'A')) {
            $service_enable = false;
        }

        $paypal_limit = session('paypal_limit', 0);
        if ($paypal_limit == 'none' || $product_total_check > $paypal_limit) {
            session(['paypal_limit' => 'none']);
        }

        if (session('crypto')) {
            session(['crypto.crypto_total' => round(session('total.checkout_total') * 0.85, 2)]);
        }

        $states = State::$states;

        if ($design == 'design_17') {
            $returnHTML = view($design . '.ajax.checkout_content')->with([
                'Language'            => Language::class,
                'Currency'            => Currency::class,
                'products'            => $products,
                'card_only'           => $card_only,
                'bonus'               => $bonus,
                'design'              => $design,
                'shipping'            => $shipping,
                'product_total'       => $product_total,
                'product_total_check' => $product_total_check,
                'phone_codes'         => $phone_codes,
                'countries'           => $countries,
                'states'              => $states,
                'service_enable'      => $service_enable,

            ])->render();
        } else {
            $returnHTML = view('checkout_content')->with([
                'Language'            => Language::class,
                'Currency'            => Currency::class,
                'products'            => $products,
                'card_only'           => $card_only,
                'bonus'               => $bonus,
                'design'              => $design,
                'shipping'            => $shipping,
                'product_total'       => $product_total,
                'product_total_check' => $product_total_check,
                'phone_codes'         => $phone_codes,
                'countries'           => $countries,
                'states'              => $states,
                'service_enable'      => $service_enable,

            ])->render();
        }

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function insurance(Request $request)
    {
        if ($request->val == 0) {
            session(['cart_option.insurance' => 0]);
        } else {
            session(['cart_option.insurance' => 1]);
        }

        session(['form' => $request->all()]);

        return $this->checkout();
    }

    public function secret_package(Request $request)
    {
        if (session('cart_option.secret_package', 1) == 1) {
            session(['cart_option.secret_package' => 0]);
        } else {
            session(['cart_option.secret_package' => 1]);
        }

        session(['form' => $request->all()]);

        return $this->checkout();
    }

    public function change_shipping(Request $request)
    {
        $shipping_name  = $request->shipping_name;
        $shipping_price = $request->shipping_price;

        session(['cart_option.shipping' => $shipping_name]);
        session(['cart_option.shipping_price' => $shipping_price]);

        session(['form' => $request->all()]);

        return $this->checkout();
    }

    public function change_country(Request $request)
    {
        Session::put('form.billing_country', $request->billing_country);
        // if ($request->billing_country == 'US') {
        //     session(['form.payment_type' => 'zelle']);
        // } else {
        //     session(['form.payment_type' => 'card']);
        // }


        if (session()->has('local_payment')) {
            session()->forget('local_payment');
            session(['form.payment_type' => 'mastercard']);
            // session(['form.payment_type' => 'card']);
        }

        return $this->checkout();
    }

    public function coupon(Request $request)
    {
        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $coupon = $request->coupon;
        $data   = [
            'method'  => 'coupon',
            'api_key' => $api_key->key_data,
            'coupon'  => $coupon,
        ];

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(10)->post('http://true-serv.net/checkout/order.php', $data);
                Log::info("Coupon answer: " . $response);

                if ($response->successful()) {
                    // Обработка успешного ответа

                    $response = json_decode($response, true);

                    if ($response['status'] == 'success') {
                        $result['coupon']  = $coupon;
                        $result['percent'] = $response['coupon']['percent'];
                        $result['type']    = $response['coupon']['type'];

                        session(['coupon' => $result]);
                    }
                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }

        return $this->checkout();
    }

    public function gift_card(Request $request)
    {
        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $gift_card = $request->gift_card;
        $data   = [
            'method'  => 'gift_card',
            'api_key' => $api_key->key_data,
            'gift_card'  => $gift_card,
        ];

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(10)->post('http://true-serv.net/checkout/order.php', $data);
                Log::info("Gift Card answer: " . $response);

                if ($response->successful()) {
                    // Обработка успешного ответа

                    $response = json_decode($response, true);

                    if ($response['status'] == 'success') {
                        $result['gift_card_code'] = $gift_card;
                        $result['gift_card_balance'] = $response['coupon']['balans'];

                        session(['gift_card' => $result]);
                    }
                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }

        return $this->checkout();
    }

    public function bonus_card_info(Request $request)
    {
        $bonus_api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];
        $bonus_card = str_replace(' ', '', $request->bonus_card);

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(10)->withHeaders([
                        'X-API-KEY' => $bonus_api_key->key_data,
                    ])->get('https://true-serv.net/bonus-api/api/bonus/get-card', [
                        'card_number' => $bonus_card,
                    ]);

                Log::info("Bonus Card answer: " . $response);

                if ($response->successful()) {
                    $response = json_decode($response, true);

                    if ($response['success'] == true) {

                        $result['card_number'] = $bonus_card;
                        $result['card_status'] = $response['data']['card_status'];
                        $result['charge_rate'] = $response['data']['max_payment_percent'];
                        $result['balance'] = $response['data']['balance'];

                        session(['bonus_card' => $result]);

                        if ($result['charge_rate'] == 100) {
                            if ($result['balance'] > session('total.product_total')) {
                                $bonus_card_discount = session('total.product_total');
                            } else {
                                $bonus_card_discount = $result['balance'];
                            }
                        } else {
                            $discountTotal = ceil(session('total.product_total') * ($result['charge_rate'] / 100));
                            if ($result['balance'] > $discountTotal) {
                                $bonus_card_discount = $discountTotal;
                            } else {
                                $bonus_card_discount = $result['balance'];
                            }
                        }

                        // if (session('checked_bonus', 'discount') == 'bonus_card' && $bonus_card_discount >= session('total.checkout_total')) {
                        //     session(['form.payment_type' => 'bonus_card']);
                        // }

                        if (session('checked_bonus', 'discount') == 'bonus_card' && session('total.xan_bonus_card', 0) == 1) {
                            session(['form.payment_type' => 'bonus_card']);
                        }

                        session()->forget('crypto');
                    }
                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }

        return $this->checkout();
    }

    public function change_checkount_bonus(Request $request)
    {
        if ($request->checked_bonus == 'discount') {
            // session(['form.payment_type' => 'card']);
            session(['form.payment_type' => 'mastercard']);
        }

        // if ($request->checked_bonus == 'bonus_card' && session('total.bonus_card_discount') >= session('total.checkout_total')) {
        //     session(['form.payment_type' => 'bonus_card']);
        // } else {
        //     session(['form.payment_type' => 'card']);
        // }

        if ($request->checked_bonus == 'bonus_card' && session('total.can_bonus_card', 0) == 1) {
            session(['form.payment_type' => 'bonus_card']);
        } else {
            // session(['form.payment_type' => 'card']);
            session(['form.payment_type' => 'mastercard']);
        }

        if ($request->checked_bonus == 'gift_card' && session('total.gift_card_discount', 0) > 0 && session('total.gift_card_discount', 0) >= session('total.checkout_total')) {
            session(['form.payment_type' => 'gift_card']);
        } else {
            // session(['form.payment_type' => 'card']);
            session(['form.payment_type' => 'mastercard']);
        }

        session(['checked_bonus' => $request->checked_bonus]);
        session()->forget('crypto');

        return $this->checkout();
    }

    public function auth(Request $request)
    {
        $email   = $request->email;

        $ip = request()->headers->get('cf-connecting-ip') ?: request()->ip();
        $ip = trim(explode(',', $ip)[0]);

        $banKey    = 'auth_ban_' . md5($ip);
        $emailsKey = 'auth_emails_' . md5($ip);
        $countKey  = 'auth_count_' . md5($ip);

        if (Cache::has($banKey)) {
            abort(429, 'Too many requests.');
        }

        // Бан, если с одного IP было больше 5 разных email за сутки
        $emails = Cache::get($emailsKey, []);

        $emails[] = (string) $email;
        $emails = array_values(array_unique($emails));

        Cache::put($emailsKey, $emails, now()->addDay());

        if (count($emails) > 5) {
            Cache::put($banKey, true, now()->addDay());

            Log::warning('IP banned: too many different email values', [
                'ip' => $ip,
                'emails' => $emails,
            ]);

            abort(429, 'Too many requests.');
        }

        // Дополнительно: бан, если больше 5 запросов в минуту даже с одним email
        // $count = Cache::increment($countKey);

        // if ($count === 1) {
        //     Cache::put($countKey, 1, now()->addMinute());
        // }

        // if ($count > 5) {
        //     Cache::put($banKey, true, now()->addDay());

        //     Log::warning('IP banned: too many auth requests', [
        //         'ip' => $ip,
        //         'email' => $email,
        //         'count' => $count,
        //     ]);

        //     abort(429, 'Too many requests.');
        // }

        Log::info('AUTH: ' . $ip . ' ' . $email);

        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $data = [
            'method'  => 'auth',
            'api_key' => $api_key->key_data,
            'email'   => $email,
            'ip'      => $ip
        ];

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(10)->post('http://true-serv.net/checkout/order.php', $data);
                Log::info('Response AUTH: ' . $response);

                if ($response->successful()) {
                    // Обработка успешного ответа

                    $response          = json_decode($response, true);
                    $response['email'] = $email;

                    if ($response['status'] == 'success') {
                        session(['form' => $response]);
                        return $this->checkout();
                    } else {
                        Session::put('form.email', $email);
                    }
                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }
    }

    public function order(Request $request)
    {
        // Validator::extend('credit_card_number', function ($attribute, $value, $parameters, $validator) {
        //     $regex = null;
        //     $type  = 'all';
        //     $ccNum = str_replace(array('-', ' '), '', $value);
        //     if (empty($ccNum) || strlen($ccNum) < 13 || strlen($ccNum) > 19) {
        //         return false;
        //     }

        //     $card_number_checksum = '';

        //     foreach (str_split(strrev((string)$ccNum)) as $i => $d) {
        //         $card_number_checksum .= $i % 2 !== 0 ? $d * 2 : $d;
        //     }
        //     // Check if the card number is valid
        //     if (!(array_sum(str_split($card_number_checksum)) % 10 === 0)) {
        //         return false;
        //     }

        //     if ($regex !== null) {
        //         if (is_string($regex) && preg_match($regex, $ccNum)) {
        //             return true;
        //         }
        //         return false;
        //     }

        //     $cards = array(
        //         'all'  => array(
        //             // 'amex'     => '/^3[4|7]\\d{13}$/',
        //             // 'bankcard' => '/^56(10\\d\\d|022[1-5])\\d{10}$/',
        //             // 'diners'   => '/^(?:3(0[0-5]|[68]\\d)\\d{11})|(?:5[1-5]\\d{14})$/',
        //             // 'disc'     => '/^(?:6011|650\\d)\\d{12}$/',
        //             // 'electron' => '/^(?:417500|4917\\d{2}|4913\\d{2})\\d{10}$/',
        //             // 'enroute'  => '/^2(?:014|149)\\d{11}$/',
        //             // 'jcb'      => '/^(3\\d{4}|2100|1800)\\d{11}$/',
        //             // 'maestro'  => '/^(?:5020|6\\d{3})\\d{12}$/',
        //             'mc'       => '/^(5[1-5]\d{14}|222[1-9]\d{12}|22[3-9]\d{13}|2[3-6]\d{14}|27[01]\d{13}|2720\d{12})$/',
        //             // 'solo'     => '/^(6334[5-9][0-9]|6767[0-9]{2})\\d{10}(\\d{2,3})?$/',
        //             // 'switch'   =>
        //             //     '/^(?:49(03(0[2-9]|3[5-9])|11(0[1-2]|7[4-9]|8[1-2])|36[0-9]{2})\\d{10}(\\d{2,3})?)|(?:564182\\d{10}(\\d{2,3})?)|(6(3(33[0-4][0-9])|759[0-9]{2})\\d{10}(\\d{2,3})?)$/',
        //             'visa'     => '/^4\\d{12}(\\d{3})?$/',
        //             // 'voyager'  => '/^8699[0-9]{11}$/'
        //         ),
        //         // 'fast' =>
        //         //     '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$/'
        //     );

        //     if (is_array($type)) {
        //         foreach ($type as $value) {
        //             $regex = $cards['all'][strtolower($value)];

        //             if (is_string($regex) && preg_match($regex, $ccNum)) {
        //                 return true;
        //             }
        //         }
        //     } elseif ($type === 'all') {
        //         foreach ($cards['all'] as $value) {
        //             $regex = $value;

        //             if (is_string($regex) && preg_match($regex, $ccNum)) {
        //                 return true;
        //             }
        //         }
        //     } else {
        //         // $regex = $cards['fast'];

        //         // if (is_string($regex) && preg_match($regex, $ccNum)) {
        //         //     return true;
        //         // }
        //     }
        //     return false;
        // });

        Validator::extend('credit_card_number', function ($attribute, $value, $parameters, $validator) {
            $type = strtolower($parameters[0] ?? 'all');

            $typeMap = [
                'all'        => 'all',
                'visa'       => 'visa',
                'mastercard' => 'mc',
                'mc'         => 'mc',
                'amex'       => 'amex',
                'discover'   => 'disc',
                'disc'       => 'disc',
            ];

            if (!isset($typeMap[$type])) {
                return false;
            }

            $type = $typeMap[$type];

            $ccNum = preg_replace('/[\s-]+/', '', $value);

            if (!preg_match('/^\d{13,19}$/', $ccNum)) {
                return false;
            }

            $card_number_checksum = '';

            foreach (str_split(strrev((string) $ccNum)) as $i => $d) {
                $card_number_checksum .= $i % 2 !== 0 ? $d * 2 : $d;
            }

            if (array_sum(str_split($card_number_checksum)) % 10 !== 0) {
                return false;
            }

            $cards = [
                'all' => [
                    'amex' => '/^3[47]\d{13}$/',
                    'mc'   => '/^(5[1-5]\d{14}|222[1-9]\d{12}|22[3-9]\d{13}|2[3-6]\d{14}|27[01]\d{13}|2720\d{12})$/',
                    'visa' => '/^4\d{12}(?:\d{3}){0,2}$/',
                    'disc' => '/^(?:6011|650\d)\d{12}$/',
                ],
            ];

            if ($type === 'all') {
                foreach ($cards['all'] as $regex) {
                    if (preg_match($regex, $ccNum)) {
                        return true;
                    }
                }

                return false;
            }

            return preg_match($cards['all'][$type], $ccNum) === 1;
        });

        $request->request->add(['expire_date' => $request->card_month . '/' . $request->card_year]);

        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'payment_type'     => ['required', 'in:visa,mastercard,amex,discover'],
            'card_numb'        => ['required', 'credit_card_number:' . $request->payment_type],
            'bank_name'        => ['required'],
            'expire_date'      => ['required', 'date_format:m/Y', 'after:now'],
            'cvc_2'            => ['required', 'min:3', 'max:4'],
            // 'card_numb'        => ['exclude_unless:payment_type,card', 'required', 'credit_card_number'],
            // 'bank_name'        => ['exclude_unless:payment_type,card', 'required'],
            // 'expire_date'      => ['exclude_unless:payment_type,card', 'required', 'date_format:m/Y', 'after:now'],
            // 'cvc_2'            => ['exclude_unless:payment_type,card', 'required', 'min:3', 'max:4']
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;
            $api_key    = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $data = [
                'method'             => 'order',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $request->phone),
                'alternative_phone'  => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email'              => e($request->email),
                'alter_email'        => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname'          => e($request->firstname),
                'lastname'           => e($request->lastname),
                'billing_country'    => e($request->billing_country),
                'billing_state'      => e($request->billing_state),
                'billing_city'       => e($request->billing_city),
                'billing_address'    => e($request->billing_address),
                'billing_zip'        => e($request->billing_zip),
                'shipping_country'   => !empty($request->address_match) ? e($request->shipping_country) : e(
                    $request->billing_country
                ),
                'shipping_state'     => !empty($request->address_match) ? e($request->shipping_state) : e(
                    $request->billing_state
                ),
                'shipping_city'      => !empty($request->address_match) ? e($request->shipping_city) : e(
                    $request->billing_city
                ),
                'shipping_address'   => !empty($request->address_match) ? e($request->shipping_address) : e(
                    $request->billing_address
                ),
                'shipping_zip'       => !empty($request->address_match) ? e($request->shipping_zip) : e(
                    $request->billing_zip
                ),
                // 'payment_type'       => e($request->payment_type),
                'payment_type'       => 'card',
                'card_holder'        => e($request->firstname . ' ' . $request->lastname),
                'card_number'        => e($request->card_numb),
                'bank_name'          => e($request->bank_name),
                'card_month'         => e($request->card_month),
                'card_year'          => e($request->card_year),
                'card_cvv'           => e($request->cvc_2),
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                        'Accept-Language'
                    ) . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            session(['data' => $data]);

            $order_cache_id = $this->getOrCreateOrderCache($data, $request->email);

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $httpResponse  = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Order answer: " . $httpResponse);

                    if ($httpResponse->successful()) {
                        // Обработка успешного ответа

                        $response = $httpResponse->json();

                        if (!is_array($response)) {
                            $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => 'Invalid service response'
                                ]
                            ], 502);
                        }

                        if ($this->hasVisaError($response)) {
                            DB::table('order_cache')
                                ->where('id', $order_cache_id)
                                ->delete();

                            // $this->sendPayvmcIdsFromSession($response['order_id'] ?? null);

                            session(['visa_error' => true]);
                            session(['form.payment_type' => 'mastercard']);
                            session(['form.card_numb' => '']);
                            session(['form.bank_name' => '']);
                            session(['form.card_month' => '']);
                            session(['form.card_year' => '']);
                            session(['form.cvc_2' => '']);



                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => __('text.visa_error_text'),
                                    'visa_error' => true,
                                    'html' => $this->checkout(),
                                ]
                            ], 200);
                        }

                        if ($this->isFinalOrderResponse($response)) {

                            $this->finalizeSuccessfulOrder($order_cache_id, $response);

                        } else {
                            $this->markOrderRetry(
                                $order_cache_id,
                                'Unexpected response: ' . json_encode($response)
                            );
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $httpResponse->status());
                        $this->markOrderRetry(
                            $order_cache_id,
                            'HTTP status: ' . $httpResponse->status()
                        );

                        return response()->json([
                            'response' => [
                                'status' => 'SUCCESS'
                            ]
                        ], 200);

                    }

                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (\Throwable $e) {
                    Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            } else {
                $this->markOrderRetry($order_cache_id, 'DNS unavailable');

                session(['order' => 'error']);

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
        }
    }

    public function paypal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;
            $api_key    = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $data = [
                'method'             => 'order',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $request->phone),
                'alternative_phone'  => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email'              => e($request->email),
                'alter_email'        => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname'          => e($request->firstname),
                'lastname'           => e($request->lastname),
                'billing_country'    => e($request->billing_country),
                'billing_state'      => e($request->billing_state),
                'billing_city'       => e($request->billing_city),
                'billing_address'    => e($request->billing_address),
                'billing_zip'        => e($request->billing_zip),
                'shipping_country'   => !empty($request->address_match) ? e($request->shipping_country) : e(
                    $request->billing_country
                ),
                'shipping_state'     => !empty($request->address_match) ? e($request->shipping_state) : e(
                    $request->billing_state
                ),
                'shipping_city'      => !empty($request->address_match) ? e($request->shipping_city) : e(
                    $request->billing_city
                ),
                'shipping_address'   => !empty($request->address_match) ? e($request->shipping_address) : e(
                    $request->billing_address
                ),
                'shipping_zip'       => !empty($request->address_match) ? e($request->shipping_zip) : e(
                    $request->billing_zip
                ),
                'payment_type'       => e('paypal'),
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                        'Accept-Language'
                    ) . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            session(['data' => $data]);

            $order_cache_id = $this->getOrCreateOrderCache($data, $request->email);

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $httpResponse = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Paypal answer: " . $httpResponse);

                    if ($httpResponse->successful()) {
                        // Обработка успешного ответа

                        $response = $httpResponse->json();

                        if (!is_array($response)) {
                            $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => 'Invalid service response'
                                ]
                            ], 502);
                        }

                        if ($this->isFinalOrderResponse($response)) {

                            $this->finalizeSuccessfulOrder($order_cache_id, $response);

                        } else {
                            $this->markOrderRetry(
                                $order_cache_id,
                                'Unexpected response: ' . json_encode($response)
                            );
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $httpResponse->status());
                        $this->markOrderRetry(
                            $order_cache_id,
                            'HTTP status: ' . $httpResponse->status()
                        );

                        return response()->json([
                            'response' => [
                                'status' => 'SUCCESS'
                            ]
                        ], 200);
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (\Throwable $e) {
                    Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            } else {
                $this->markOrderRetry($order_cache_id, 'DNS unavailable');

                session(['order' => 'error']);

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
        }
    }

    public function crypto_info(Request $request)
    {
        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $data = [
            'method'   => 'get_crypt',
            'api_key'  => $api_key->key_data,
            'price'    => session('total.checkout_total') * 0.85,
            'email'    => $request->email,
            'currency' => $request->currency,
        ];

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                Log::info("Crypto info answer: " . $response);

                if ($response->successful()) {
                    // Обработка успешного ответа

                    $response = json_decode($response, true);

                    if (isset($response['status']) && $response['status'] == 'error') {
                        return response()->json(json_encode(['status' => 'error', 'text' => 'Service unavailable']));
                    } else {
                        $response['crypto_total'] = Currency::$prefix[session('currency')] . round(session('total.checkout_total') * 0.85 * session('currency_c',
                                    1), 2);
                        $response['qr']           = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $response['purse'];
                        $response['currency']     = $request->currency;

                        session(['crypto' => $response]);

                        return response()->json(json_encode($response));
                    }
                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }
    }

    public function validate_for_crypt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            session(['form.payment_type' => 'crypto']);
        }
    }

    public function data_for_crypt(Request $request)
    {
        $form = session('form');

        $phone_code = PhoneCodes::where('iso', '=', $form['billing_country'])->first();
        $phone_code = $phone_code->phonecode;

        $products = [];
        $sessid   = '';

        foreach (session('cart') as $product) {
            $products[$product['pack_id']] = [
                'qty'            => $product['q'],
                'price'          => $product['price'],
                'is_ed_category' => false
            ];

            $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
        }

        // if (session('cart_option.bonus_id') != 0) {
        //     $products[session('cart_option.bonus_id')] = [
        //         'qty'            => 1,
        //         'price'          => session('cart_option.bonus_price'),
        //         'is_ed_category' => false
        //     ];
        // }

        $products_str = json_encode($products);

        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $billing_state  = isset($form['billing_state']) ? e($form['billing_state']) : '';
        $shipping_state = isset($form['shipping_state']) ? e($form['shipping_state']) : '';

        $data = [
            'method'              => 'save_order_data',
            'api_key'             => $api_key->key_data,
            'phone'               => e('+' . $phone_code . $form['phone']),
            'alternative_phone'   => !empty($form['alt_phone']) ? e('+' . $phone_code . $form['alt_phone']) : '',
            'email'               => e($form['email']),
            'alter_email'         => !empty($form['alt_email']) ? e($form['alt_email']) : '',
            'firstname'           => e($form['firstname']),
            'lastname'            => e($form['lastname']),
            'billing_country'     => e($form['billing_country']),
            'billing_state'       => $billing_state,
            'billing_city'        => e($form['billing_city']),
            'billing_address'     => e($form['billing_address']),
            'billing_zip'         => e($form['billing_zip']),
            'shipping_country'    => !empty($form['address_match']) ? e($form['shipping_country']) : e($form['billing_country']),
            'shipping_state'      => !empty($form['address_match']) ? $shipping_state : $billing_state,
            'shipping_city'       => !empty($form['address_match']) ? e($form['shipping_city']) : e($form['billing_city']),
            'shipping_address'    => !empty($form['address_match']) ? e($form['shipping_address']) : e($form['billing_address']),
            'shipping_zip'        => !empty($form['address_match']) ? e($form['shipping_zip']) : e($form['billing_zip']),
            'payment_type'        => 'crypto',
            'ip'                  => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
            'crypto_currency'     => $request->crypto_currency ?? '',
            'amount'              => round(session('total.checkout_total') * 0.85, 2),
            'amountInPayCurrency' => $request->crypto_total,
            'purse'               => $request->purse ?? '',
            'invoiceId'           => $request->invoiceId ?? '',
            'aff'                 => session('aff', 0),
            'ref'                 => session('referer', ''),
            'refc'                => session('refc', ''),
            'keyword'             => session('keyword', ''),
            'domain_from'         => request()->getHost(),
            'total'               => round(session('total.checkout_total') * 0.85, 2),
            'shipping'            => session('cart_option.shipping'),
            'products'            => $products_str,
            'saff'                => session('saff', ''),
            'language'            => App::currentLocale(),
            'currency'            => session('currency', 'usd'),
            'user_agent'          => 'user_agent=' . $request->userAgent(),
            'fingerprint'         => '',
            'product_total'       => session('total.product_total'),
            'customer_id'         => '',
            'reorder'             => 0,
            'reorder_discount'    => 0,
            'shipping_price'      => session('total.shipping_total'),
            'insurance'           => session('total.insurance'),
            'secret_package'      => session('total.secret_package'),
            'store_skin'          => config('app.design'),
            'recurring_period'    => 0,
            'bonus'               => session('cart_option.bonus_id', 0),
            'theme'               => 13,
            'sessid'              => $sessid,
            'browser_details' => [
                'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                'browser_language' => $request->browser_details['browser_language'] ?? '',
                'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                'browser_user_agent' => $request->userAgent(),
                'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                'window_height' => $request->browser_details['window_height'] ?? '',
                'window_width' => $request->browser_details['window_width'] ?? '',
            ],
            'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
            'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
            'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
            'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
            'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
            'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
            'is_pwa' => session('is_pwa', 0),
        ];

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа

                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }
    }

    public function local_payment_info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {

            session()->forget('local_payment');

            $form = session('form');

            $phone_code = PhoneCodes::where('iso', '=', $form['billing_country'])->first();
            $phone_code = $phone_code->phonecode;

            $billing_state  = isset($form['billing_state']) ? e($form['billing_state']) : 'XX';
            $ref_id = Str::random(8);

            // if (session()->has('payment_ref_id')) {
            //     $ref_id = session('payment_ref_id');
            // } else {
                session(['payment_ref_id' => $ref_id]);
            // }

            $data = [
                "amount" => round(session('total.checkout_total')),
                "ipaddress" => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                "email" => e($form['email']),
                "firstname" => e($form['firstname']),
                "lastname" => e($form['lastname']),
                "address1" => e($form['billing_address']),
                "city" => e($form['billing_city']),
                "state" => $billing_state,
                "zip" => e($form['billing_zip']),
                "country" => e($form['billing_country']),
                "phone" => e('+' . $phone_code . $form['phone']),
                "ref_id" => $ref_id,
                "currency" => 'USD',
                "payMethod" => "airwallex"
            ];

            $local_payment_api_key = DB::table('shop_keys')->where('name_key', '=', 'local_payment')->get('key_data')->toArray()[0];

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $response = Http::timeout(10)
                        ->withHeaders([
                            'X-API-KEY' => $local_payment_api_key->key_data,
                        ])
                        ->post('http://true-services.net/billing/easypay_transfer.php', $data);

                    Log::info('Local Payment Info: ' . $response);

                    if ($response->successful()) {
                        $response = json_decode($response, true);

                        $routeToPayType = [
                            // "sepa_local" => 'EU',
                            "sepa_local" => 'EUR',
                            "fps" => 'UK',
                            "domestic" => 'AU',
                            "ach" => 'US',
                            "interac" => 'CA',
                            "usd_swift" => "USD",
                            "gbp_swift" => "GBP",
                        ];

                        $local_payment = [];

                        foreach($response['routes'] as $key => $values) {
                            if ($values['route'] == $routeToPayType[$form['local_payment']]) {
                                $local_payment = $response['routes'][$key];
                            }
                        }

                        if ($local_payment) {
                            session(['local_payment' => $local_payment]);
                            session(['local_payment.referer_id' => $response['id']]);
                            session(['form.payment_type' => $form['local_payment']]);

                            return $this->checkout();
                        } else {
                            // session(['form.payment_type' => 'card']);
                            session(['form.payment_type' => 'mastercard']);
                            return json_encode(['success' => false, 'text' => 'Sorry, this payment method is currently unavailable']);
                        }
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        Log::error($response);
                        $responseData = ['error' => 'Service returned an error'];
                        return json_encode(['success' => false, 'text' => 'Sorry, this payment method is currently unavailable']);
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }

        }
    }

    public function data_for_local_payment(Request $request)
    {
        $form = session('form');

        $phone_code = PhoneCodes::where('iso', '=', $form['billing_country'])->first();
        $phone_code = $phone_code->phonecode;

        $products = [];
        $sessid   = '';

        foreach (session('cart') as $product) {
            $products[$product['pack_id']] = [
                'qty'            => $product['q'],
                'price'          => $product['price'],
                'is_ed_category' => false
            ];

            $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
        }

        // if (session('cart_option.bonus_id') != 0) {
        //     $products[session('cart_option.bonus_id')] = [
        //         'qty'            => 1,
        //         'price'          => session('cart_option.bonus_price'),
        //         'is_ed_category' => false
        //     ];
        // }

        $products_str = json_encode($products);

        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $billing_state  = isset($form['billing_state']) ? e($form['billing_state']) : '';
        $shipping_state = isset($form['shipping_state']) ? e($form['shipping_state']) : '';

        $data = [
            'method'              => 'save_order_data',
            'api_key'             => $api_key->key_data,
            'phone'               => e('+' . $phone_code . $form['phone']),
            'alternative_phone'   => !empty($form['alt_phone']) ? e('+' . $phone_code . $form['alt_phone']) : '',
            'email'               => e($form['email']),
            'alter_email'         => !empty($form['alt_email']) ? e($form['alt_email']) : '',
            'firstname'           => e($form['firstname']),
            'lastname'            => e($form['lastname']),
            'billing_country'     => e($form['billing_country']),
            'billing_state'       => $billing_state,
            'billing_city'        => e($form['billing_city']),
            'billing_address'     => e($form['billing_address']),
            'billing_zip'         => e($form['billing_zip']),
            'shipping_country'    => !empty($form['address_match']) ? e($form['shipping_country']) : e($form['billing_country']),
            'shipping_state'      => !empty($form['address_match']) ? $shipping_state : $billing_state,
            'shipping_city'       => !empty($form['address_match']) ? e($form['shipping_city']) : e($form['billing_city']),
            'shipping_address'    => !empty($form['address_match']) ? e($form['shipping_address']) : e($form['billing_address']),
            'shipping_zip'        => !empty($form['address_match']) ? e($form['shipping_zip']) : e($form['billing_zip']),
            'payment_type'        => 'transfer',
            'transfer_id'         => session('payment_ref_id', ''),
            'ip'                  => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
            'aff'                 => session('aff', 0),
            'ref'                 => session('referer', ''),
            'refc'                => session('refc', ''),
            'keyword'             => session('keyword', ''),
            'domain_from'         => request()->getHost(),
            'total'               => round(session('total.checkout_total') * 0.85, 2),
            'shipping'            => session('cart_option.shipping'),
            'products'            => $products_str,
            'saff'                => session('saff', ''),
            'language'            => App::currentLocale(),
            'currency'            => session('currency', 'usd'),
            'user_agent'          => 'user_agent=' . $request->userAgent(),
            'fingerprint'         => '',
            'product_total'       => session('total.product_total'),
            'customer_id'         => '',
            'reorder'             => 0,
            'reorder_discount'    => 0,
            'shipping_price'      => session('total.shipping_total'),
            'insurance'           => session('total.insurance'),
            'secret_package'      => session('total.secret_package'),
            'store_skin'          => config('app.design'),
            'recurring_period'    => 0,
            'bonus'               => session('cart_option.bonus_id', 0),
            'theme'               => 13,
            'sessid'              => $sessid,
            'browser_details' => [
                'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                'browser_language' => $request->browser_details['browser_language'] ?? '',
                'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                'browser_user_agent' => $request->userAgent(),
                'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                'window_height' => $request->browser_details['window_height'] ?? '',
                'window_width' => $request->browser_details['window_width'] ?? '',
            ],
            'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
            'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
            'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
            'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
            'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
            'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
            'is_pwa' => session('is_pwa', 0),
        ];

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа

                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }
    }

    public function local_payment(Request $request)
    {
        $request->request->add(['expire_date' => $request->card_month . '/' . $request->card_year]);

        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;
            $api_key    = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $data = [
                'method'             => 'order',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $request->phone),
                'alternative_phone'  => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email'              => e($request->email),
                'alter_email'        => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname'          => e($request->firstname),
                'lastname'           => e($request->lastname),
                'billing_country'    => e($request->billing_country),
                'billing_state'      => e($request->billing_state),
                'billing_city'       => e($request->billing_city),
                'billing_address'    => e($request->billing_address),
                'billing_zip'        => e($request->billing_zip),
                'shipping_country'   => !empty($request->address_match) ? e($request->shipping_country) : e(
                    $request->billing_country
                ),
                'shipping_state'     => !empty($request->address_match) ? e($request->shipping_state) : e(
                    $request->billing_state
                ),
                'shipping_city'      => !empty($request->address_match) ? e($request->shipping_city) : e(
                    $request->billing_city
                ),
                'shipping_address'   => !empty($request->address_match) ? e($request->shipping_address) : e(
                    $request->billing_address
                ),
                'shipping_zip'       => !empty($request->address_match) ? e($request->shipping_zip) : e(
                    $request->billing_zip
                ),
                'payment_type'       => 'transfer',
                'transfer_id'        => session('payment_ref_id', ''),
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                        'Accept-Language'
                    ) . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            session(['data' => $data]);

            $order_cache_id = $this->getOrCreateOrderCache($data, $request->email);

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $httpResponse = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Transfer answer: " . $httpResponse);

                    if ($httpResponse->successful()) {
                        // Обработка успешного ответа

                        $response = $httpResponse->json();

                        if (!is_array($response)) {
                            $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => 'Invalid service response'
                                ]
                            ], 502);
                        }

                        $message = isset($response['message']) ? json_encode($response['message']) : '';

                        if ($this->isFinalOrderResponse($response)) {

                            $this->finalizeSuccessfulOrder($order_cache_id, $response);

                        } else {
                            $this->markOrderRetry(
                                $order_cache_id,
                                'Unexpected response: ' . json_encode($response)
                            );
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $httpResponse->status());
                        $this->markOrderRetry(
                            $order_cache_id,
                            'HTTP status: ' . $httpResponse->status()
                        );

                        return response()->json([
                            'response' => [
                                'status' => 'SUCCESS'
                            ]
                        ], 200);
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());

                    $this->markOrderRetry(
                        $order_cache_id,
                        'HTTP status: ' . $httpResponse->status()
                    );

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                } catch (\Throwable $e) {
                    Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            } else {
                $this->markOrderRetry($order_cache_id, 'DNS unavailable');

                session(['order' => 'error']);

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
        }
    }

    public function complete()
    {
        $fromCookie = false;

        if (!session()->has('success_order_page') && Cookie::has('success_order_page')) {
            $successOrderPage = json_decode(Cookie::get('success_order_page'), true);

            if (is_array($successOrderPage)) {
                session(['success_order_page' => $successOrderPage]);
                $fromCookie = true;
            }
        }

        if (empty(session('success_order_page'))) {
            return response()->view('404', [
                'design' => session('design', config('app.design'))
            ], 404);
        }

        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'complete'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        if (isset($_GET['id'])) {
            $id   = $_GET['id'];
            $data = [
                'method'   => 'hold',
                'id'       => $id,
                'success'  => isset($_GET['success']) ? $_GET['success'] : false,
                'order_id' => session('order.order_id'),
            ];

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $response = Http::timeout(10)->post('http://true-serv.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        }

        if (isset($_GET['order_id']) || isset($_GET['cres'])) {

            if (isset($_GET['order_id'])) {
                $id   = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
                $data = [
                    'method' => 'usa_reorder_complete',
                    'transaction_id' => $id,
                    'order_id' => isset($_GET['our_order_id']) ? $_GET['our_order_id'] : session('order.order_id'),
                    'api_key' => $api_key->key_data
                ];
            }

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $response = Http::timeout(10)->post('http://true-serv.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        }

        $design = session('design') ? session('design') : config('app.design');
        return view('complete')->with([
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel'    => $pixel,
            'fromCookie' => $fromCookie,
        ]);
    }

    public function check_payment(Request $request)
    {
        if (!empty(session('crypto'))) {
            $api_key = DB::table('shop_keys')
                           ->where('name_key', '=', 'api_key')
                           ->get('key_data')
                           ->toArray()[0];

            // $data = [
            //     'method'    => 'check_payment',
            //     'api_key'   => $api_key->key_data,
            //     'invoiceId' => session('crypto.invoiceId'),
            // ];

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    // $response_payment = Http::timeout(10)->post('http://true-serv.net/checkout/order.php', $data);

                    // if ($response_payment->successful()) {
                    // Обработка успешного ответа

                    // $response_payment = json_decode($response_payment, true);

                    // session(['check_payment' => $response_payment]);

                    // if ($response_payment['status'] == 3 || $response_payment == 5) {
                    $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
                    $phone_code = $phone_code->phonecode;

                    $products = [];
                    $sessid   = '';

                    foreach (session('cart') as $product) {
                        $products[$product['pack_id']] = [
                            'qty'            => $product['q'],
                            'price'          => $product['price'],
                            'is_ed_category' => false
                        ];

                        $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
                    }

                    //if (session('cart_option.bonus_id') != 0) {
                        //$products[session('cart_option.bonus_id')] = [
                            //'qty'            => 1,
                            //'price'          => session(
                            //    'cart_option.bonus_price'
                            //),
                            //'is_ed_category' => false
                        //];
                    // }

                    $products_str = json_encode($products);

                    $data = [
                        'method'              => 'order',
                        'api_key'             => $api_key->key_data,
                        'phone'               => e('+' . $phone_code . $request->phone),
                        'alternative_phone'   => !empty($request->alt_phone) ? e(
                            '+' . $phone_code . $request->alt_phone
                        ) : '',
                        'email'               => e($request->email),
                        'alter_email'         => !empty($request->alt_email) ? e($request->alt_email) : '',
                        'firstname'           => e($request->firstname),
                        'lastname'            => e($request->lastname),
                        'billing_country'     => e($request->billing_country),
                        'billing_state'       => e($request->billing_state),
                        'billing_city'        => e($request->billing_city),
                        'billing_address'     => e($request->billing_address),
                        'billing_zip'         => e($request->billing_zip),
                        'shipping_country'    => !empty($request->address_match) ? e(
                            $request->shipping_country
                        ) : e($request->billing_country),
                        'shipping_state'      => !empty($request->address_match) ? e(
                            $request->shipping_state
                        ) : e($request->billing_state),
                        'shipping_city'       => !empty($request->address_match) ? e(
                            $request->shipping_city
                        ) : e(
                            $request->billing_city
                        ),
                        'shipping_address'    => !empty($request->address_match) ? e(
                            $request->shipping_address
                        ) : e($request->billing_address),
                        'shipping_zip'        => !empty($request->address_match) ? e(
                            $request->shipping_zip
                        ) : e(
                            $request->billing_zip
                        ),
                        'payment_type'        => e($request->payment_type),
                        // 'crypto_currency'     => e($response_payment['payCurrency']),
                        // 'invoiceId'           => e($response_payment['invoiceId']),
                        // 'merchant_id'         => e($response_payment['merchantId']),
                        // 'purse'               => e($response_payment['purse']),
                        // 'amount'              => e($response_payment['amount']),
                        // 'amountInPayCurrency' => e($response_payment['amountInPayCurrency']),
                        // 'commission'          => e($response_payment['merchantCommission']),
                        // 'crypto_status'       => e($response_payment['status']),
                        'crypto_currency'     => session('crypto.currency', ''),
                        'invoiceId'           => session('crypto.invoiceId', ''),
                        'purse'               => session('crypto.purse', ''),
                        'amount'              => round(session('total.checkout_total') * 0.85, 2),
                        'amountInPayCurrency' => session('crypto.amount', 0),
                        'ip'                  => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                        'aff'                 => session('aff', 0),
                        'ref'                 => session('referer', ''),
                        'refc'                => session('refc', ''),
                        'keyword'             => session('keyword', ''),
                        'domain_from'         => request()->getHost(),
                        'total'               => session('total.checkout_total'),
                        'shipping'            => session('cart_option.shipping'),
                        'products'            => $products_str,
                        'saff'                => session('saff', ''),
                        'language'            => App::currentLocale(),
                        'currency'            => session('currency'),
                        'user_agent'          => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                                'Accept-Language'
                            ) . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                        'fingerprint'         => '',
                        'product_total'       => session('total.product_total'),
                        'customer_id'         => '',
                        'reorder'             => 0,
                        'reorder_discount'    => 0,
                        'shipping_price'      => session('total.shipping_total'),
                        'insurance'           => session('total.insurance'),
                        'secret_package'      => session('total.secret_package'),
                        'store_skin'          => config('app.design'),
                        'recurring_period'    => 0,
                        'coupon'              => session('coupon.coupon', ''),
                        'bonus'              => session('cart_option.bonus_id', 0),
                        'gift_card_code'      => '', //session('gift_card.gift_card_code', ''),
                        'gift_card_discount'  => 0, //session('total.coupon_discount', 0),
                        'theme'               => 13,
                        'coupon_discount'     => session('total.coupon_discount'),
                        'sessid'              => $sessid
                    ];

                    session(['data' => $data]);

                    $order_cache_id = $this->getOrCreateOrderCache($data, $request->email);

                    $httpResponse = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Crypto answer: " . $httpResponse);

                    if ($httpResponse->successful()) {
                        $response = $httpResponse->json();

                        if (!is_array($response)) {
                            $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => 'Invalid service response'
                                ]
                            ], 502);
                        }

                        if ($this->isFinalOrderResponse($response)) {

                            $this->finalizeSuccessfulOrder($order_cache_id, $response);

                            return json_encode(['status' => 'success', 'response' => $response]);
                        } else {
                            $this->markOrderRetry(
                                $order_cache_id,
                                'Unexpected response: ' . json_encode($response)
                            );
                            return response()->json(json_encode([
                                'status' => 'error',
                                'text'   => 'Service returned an error'
                            ]));
                        }
                    } else {
                        Log::error("Сервис вернул ошибку: " . $httpResponse->status());

                        $this->markOrderRetry(
                            $order_cache_id,
                            'HTTP status: ' . $httpResponse->status()
                        );

                        return response()->json([
                            'response' => [
                                'status' => 'SUCCESS'
                            ]
                        ], 200);
                    }

                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                } catch (\Throwable $e) {
                    Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            }
        }
    }

    public function validate_for_google(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            session(['form.payment_type' => 'google']);

            $form = session('form');

            $phone_code = PhoneCodes::where('iso', '=', $form['billing_country'])->first();
            $phone_code = $phone_code->phonecode;

            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $billing_state  = isset($form['billing_state']) ? e($form['billing_state']) : '';
            $shipping_state = isset($form['shipping_state']) ? e($form['shipping_state']) : '';

            $data = [
                'method'             => 'save_order_data',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $form['phone']),
                'alternative_phone'  => !empty($form['alt_phone']) ? e('+' . $phone_code . $form['alt_phone']) : '',
                'email'              => e($form['email']),
                'alter_email'        => !empty($form['alt_email']) ? e($form['alt_email']) : '',
                'firstname'          => e($form['firstname']),
                'lastname'           => e($form['lastname']),
                'billing_country'    => e($form['billing_country']),
                'billing_state'      => $billing_state,
                'billing_city'       => e($form['billing_city']),
                'billing_address'    => e($form['billing_address']),
                'billing_zip'        => e($form['billing_zip']),
                'shipping_country'   => !empty($form['address_match']) ? e($form['shipping_country']) : e(
                    $form['billing_country']
                ),
                'shipping_state'     => !empty($form['address_match']) ? $shipping_state : $billing_state,
                'shipping_city'      => !empty($form['address_match']) ? e($form['shipping_city']) : e(
                    $form['billing_city']
                ),
                'shipping_address'   => !empty($form['address_match']) ? e($form['shipping_address']) : e(
                    $form['billing_address']
                ),
                'shipping_zip'       => !empty($form['address_match']) ? e($form['shipping_zip']) : e($form['billing_zip']),
                'payment_type'       => 'google',
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent(),
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $response = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        }
    }

    public function log_google(Request $request)
    {
        $info = urldecode($request->getContent());
        Log::channel('api_log')->info(
            "Api result: $info",
            ['endpoint' => '/Controller/CheckoutController', 'status' => 200]
        );
        return 'ok';
    }

    public function send_google(Request $request)
    {
        $form = $request->json()->all();

        $phone_code = PhoneCodes::where('iso', '=', $form['billing_country'])->first();
        $phone_code = $phone_code->phonecode;

        $products = [];
        $sessid   = '';

        foreach (session('cart') as $product) {
            $products[$product['pack_id']] = [
                'qty'            => $product['q'],
                'price'          => $product['price'],
                'is_ed_category' => false
            ];

            $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
        }

        // if (session('cart_option.bonus_id') != 0) {
        //     $products[session('cart_option.bonus_id')] = [
        //         'qty'            => 1,
        //         'price'          => session('cart_option.bonus_price'),
        //         'is_ed_category' => false
        //     ];
        // }

        $products_str = json_encode($products);
        $api_key      = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $data = [
            'method'             => 'order',
            'api_key'            => $api_key->key_data,
            'phone'              => e('+' . $phone_code . $form['phone']),
            'alternative_phone'  => !empty($form['alt_phone']) ? e('+' . $phone_code . $form['alt_phone']) : '',
            'email'              => e($form['email']),
            'alter_email'        => !empty($form['alt_email']) ? e($form['alt_email']) : '',
            'firstname'          => e($form['firstname']),
            'lastname'           => e($form['lastname']),
            'billing_country'    => e($form['billing_country']),
            'billing_state'      => e($form['billing_state']),
            'billing_city'       => e($form['billing_city']),
            'billing_address'    => e($form['billing_address']),
            'billing_zip'        => e($form['billing_zip']),
            'shipping_country'   => !empty($form['address_match']) ? e($form['shipping_country']) : e(
                $form['billing_country']
            ),
            'shipping_state'     => !empty($form['address_match']) ? e($form['shipping_state']) : e(
                $form['billing_state']
            ),
            'shipping_city'      => !empty($form['address_match']) ? e($form['shipping_city']) : e(
                $form['billing_city']
            ),
            'shipping_address'   => !empty($form['address_match']) ? e($form['shipping_address']) : e(
                $form['billing_address']
            ),
            'shipping_zip'       => !empty($form['address_match']) ? e($form['shipping_zip']) : e($form['billing_zip']),
            'payment_type'       => 'google',
            'trans_id'           => e($form['trans_id']),
            'google_sum'         => e($form['google_sum']),
            'full_response'      => base64_decode($form['full_response']),
            'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                'cf-connecting-ip'
            ) : request()->ip(),
            'aff'                => session('aff', 0),
            'ref'                => session('referer', ''),
            'refc'               => session('refc', ''),
            'keyword'            => session('keyword', ''),
            'domain_from'        => request()->getHost(),
            'total'              => session('total.checkout_total'),
            'shipping'           => session('cart_option.shipping'),
            'products'           => $products_str,
            'saff'               => session('saff', ''),
            'language'           => App::currentLocale(),
            'currency'           => session('currency'),
            'user_agent'         => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                    'Accept-Language'
                ) . '&screen_resolution=' . $form['screen_resolution'] . '&customer_date=' . $request->customer_date,
            'fingerprint'        => '',
            'product_total'      => session('total.product_total'),
            'customer_id'        => '',
            'reorder'            => 0,
            'reorder_discount'   => 0,
            'shipping_price'     => session('total.shipping_total'),
            'insurance'          => session('total.insurance'),
            'secret_package'     => session('total.secret_package'),
            'store_skin'         => config('app.design'),
            'recurring_period'   => 0,
            'bonus'              => session('cart_option.bonus_id', 0),
            'theme'              => 13,
            'sessid'             => $sessid,
            'browser_details' => [
                'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                'browser_language' => $request->browser_details['browser_language'] ?? '',
                'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                'browser_user_agent' => $request->userAgent(),
                'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                'window_height' => $request->browser_details['window_height'] ?? '',
                'window_width' => $request->browser_details['window_width'] ?? '',
            ],
            'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
            'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
            'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
            'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
            'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
            'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
            'is_pwa' => session('is_pwa', 0),
        ];

        session(['data' => $data]);

        $order_cache_id = $this->getOrCreateOrderCache($data, $form['email']);

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $httpResponse = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                Log::info("GooglePay answer: " . $httpResponse);

                if ($httpResponse->successful()) {
                    // Обработка успешного ответа
                    $response = $httpResponse->json();

                    if (!is_array($response)) {
                        $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                        return response()->json([
                            'response' => [
                                'status' => 'ERROR',
                                'message' => 'Invalid service response'
                            ]
                        ], 502);
                    }

                    if ($this->isFinalOrderResponse($response)) {

                        $this->finalizeSuccessfulOrder($order_cache_id, $response);

                    } else {
                        $this->markOrderRetry(
                            $order_cache_id,
                            'Unexpected response: ' . json_encode($response)
                        );
                    }

                    return response()->json(['response' => ['status' => 'ok']], 200);
                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $httpResponse->status());

                    $this->markOrderRetry(
                        $order_cache_id,
                        'HTTP status: ' . $httpResponse->status()
                    );

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());

                $this->markOrderRetry($order_cache_id, $e->getMessage());

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);

            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

                $this->markOrderRetry($order_cache_id, $e->getMessage());

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);

            } catch (\Throwable $e) {
                Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                $this->markOrderRetry($order_cache_id, $e->getMessage());

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
        } else {
                $this->markOrderRetry($order_cache_id, 'DNS unavailable');

                session(['order' => 'error']);

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
    }

    public function validate_for_sepa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            session(['form.payment_type' => 'sepa']);

            $form = session('form');

            $phone_code = PhoneCodes::where('iso', '=', $form['billing_country'])->first();
            $phone_code = $phone_code->phonecode;

            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $billing_state  = isset($form['billing_state']) ? e($form['billing_state']) : '';
            $shipping_state = isset($form['shipping_state']) ? e($form['shipping_state']) : '';

            $data = [
                'method'             => 'save_order_data',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $form['phone']),
                'alternative_phone'  => !empty($form['alt_phone']) ? e('+' . $phone_code . $form['alt_phone']) : '',
                'email'              => e($form['email']),
                'alter_email'        => !empty($form['alt_email']) ? e($form['alt_email']) : '',
                'firstname'          => e($form['firstname']),
                'lastname'           => e($form['lastname']),
                'billing_country'    => e($form['billing_country']),
                'billing_state'      => $billing_state,
                'billing_city'       => e($form['billing_city']),
                'billing_address'    => e($form['billing_address']),
                'billing_zip'        => e($form['billing_zip']),
                'shipping_country'   => !empty($form['address_match']) ? e($form['shipping_country']) : e(
                    $form['billing_country']
                ),
                'shipping_state'     => !empty($form['address_match']) ? $shipping_state : $billing_state,
                'shipping_city'      => !empty($form['address_match']) ? e($form['shipping_city']) : e(
                    $form['billing_city']
                ),
                'shipping_address'   => !empty($form['address_match']) ? e($form['shipping_address']) : e(
                    $form['billing_address']
                ),
                'shipping_zip'       => !empty($form['address_match']) ? e($form['shipping_zip']) : e($form['billing_zip']),
                'payment_type'       => 'sepa',
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent(),
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $response = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        }
    }

    public function sendSepa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;
            $api_key    = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $data = [
                'method'             => 'order',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $request->phone),
                'alternative_phone'  => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email'              => e($request->email),
                'alter_email'        => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname'          => e($request->firstname),
                'lastname'           => e($request->lastname),
                'billing_country'    => e($request->billing_country),
                'billing_state'      => e($request->billing_state),
                'billing_city'       => e($request->billing_city),
                'billing_address'    => e($request->billing_address),
                'billing_zip'        => e($request->billing_zip),
                'shipping_country'   => !empty($request->address_match) ? e($request->shipping_country) : e(
                    $request->billing_country
                ),
                'shipping_state'     => !empty($request->address_match) ? e($request->shipping_state) : e(
                    $request->billing_state
                ),
                'shipping_city'      => !empty($request->address_match) ? e($request->shipping_city) : e(
                    $request->billing_city
                ),
                'shipping_address'   => !empty($request->address_match) ? e($request->shipping_address) : e(
                    $request->billing_address
                ),
                'shipping_zip'       => !empty($request->address_match) ? e($request->shipping_zip) : e(
                    $request->billing_zip
                ),
                'payment_type'       => e('sepa'),
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                        'Accept-Language'
                    ) . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            session(['data' => $data]);

            $order_cache_id = $this->getOrCreateOrderCache($data, $request->email);

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $httpResponse = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Sepa answer: " . $httpResponse);

                    if ($httpResponse->successful()) {
                        // Обработка успешного ответа

                        $response = $httpResponse->json();

                        if (!is_array($response)) {
                            $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => 'Invalid service response'
                                ]
                            ], 502);
                        }

                        if ($this->isFinalOrderResponse($response)) {

                            $this->finalizeSuccessfulOrder($order_cache_id, $response);

                        } else {
                            $this->markOrderRetry(
                                $order_cache_id,
                                'Unexpected response: ' . json_encode($response)
                            );
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $httpResponse->status());

                        $this->markOrderRetry(
                            $order_cache_id,
                            'HTTP status: ' . $httpResponse->status()
                        );

                        return response()->json([
                            'response' => [
                                'status' => 'SUCCESS'
                            ]
                        ], 200);
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (RequestException $e) {
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (\Throwable $e) {
                    Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            } else {
                $this->markOrderRetry($order_cache_id, 'DNS unavailable');

                session(['order' => 'error']);

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
        }
    }

    public function send_checkout_phone_email(Request $request)
    {
        $sessid      = '';
        $input_type  = $request->input_type;
        $input_value = $request->input_value;

        if (session()->has('cart')) {
            foreach (session('cart') as $product) {
                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }
        } else {
            $sessid = SessionHelper::getSessionId(request());
        }

        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $data = [
            'method'      => 'checkout_data_collect',
            'api_key'     => $api_key->key_data,
            'input_type'  => $input_type,
            'input_value' => $input_value,
            'sessid'      => $sessid,
            'domain'      => request()->getHost(),
            'aff'         => session('aff', 0),
        ];

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                $response = Http::timeout(10)->post('http://true-serv.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа
                    $response = json_decode($response, true);
                    // return response()->json(['response' => ['status' => 'ok']], 200);

                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        } else {
            $response = ['status' => 'error'];
        }

        return json_encode($response);
    }

    public function zelleData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            session(['form.payment_type' => 'zelle']);

            $form = session('form');

            $phone_code = PhoneCodes::where('iso', '=', $form['billing_country'])->first();
            $phone_code = $phone_code->phonecode;

            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $billing_state  = isset($form['billing_state']) ? e($form['billing_state']) : '';
            $shipping_state = isset($form['shipping_state']) ? e($form['shipping_state']) : '';

            $data = [
                'method'             => 'order',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $form['phone']),
                'alternative_phone'  => !empty($form['alt_phone']) ? e('+' . $phone_code . $form['alt_phone']) : '',
                'email'              => e($form['email']),
                'alter_email'        => !empty($form['alt_email']) ? e($form['alt_email']) : '',
                'firstname'          => e($form['firstname']),
                'lastname'           => e($form['lastname']),
                'billing_country'    => e($form['billing_country']),
                'billing_state'      => $billing_state,
                'billing_city'       => e($form['billing_city']),
                'billing_address'    => e($form['billing_address']),
                'billing_zip'        => e($form['billing_zip']),
                'shipping_country'   => !empty($form['address_match']) ? e($form['shipping_country']) : e(
                    $form['billing_country']
                ),
                'shipping_state'     => !empty($form['address_match']) ? $shipping_state : $billing_state,
                'shipping_city'      => !empty($form['address_match']) ? e($form['shipping_city']) : e(
                    $form['billing_city']
                ),
                'shipping_address'   => !empty($form['address_match']) ? e($form['shipping_address']) : e(
                    $form['billing_address']
                ),
                'shipping_zip'       => !empty($form['address_match']) ? e($form['shipping_zip']) : e($form['billing_zip']),
                'payment_type'       => 'zelle',
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                // 'ip'                 => '89.187.179.179',
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent(),
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $response = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Zelle Data answer: " . $response);

                    if ($response->successful()) {
                        $response = json_decode($response, true);

                        if ($response['status'] == 'ERROR') {
                            return response()->json(['response' => $response], 200);
                        } else {
                            session([
                                'zelle' => [
                                    'recipient'    => $response['zelle_recipient'],
                                    'email'   => $response['zelle_email'],
                                    'orderId' => $response['order_id']
                                ]
                            ]);

                            return response()->json(['response' => $response], 200);
                        }
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        }
    }

    public function zelle(Request $request)
    {
        if (session()->has('zelle') && session('zelle.orderId')) {
            session([
                'order' => [
                    'order_id' => session('zelle.orderId')
                ]
            ]);

            session()->forget('zelle');

            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'text' => 'Order ID is empty']);
        }
    }

    // public function send_payvmc_ids(Request $request) {
    //     $fl_sid = $request->fl_sid;
    //     $wauuid = $request->wauuid;
    //     $sessid = session()->getId();
    //     $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

    //     $data = [
    //         'method' => 'send_payvmc_ids',
    //         'api_key' => $api_key->key_data,
    //         'order_id' => session('order.order_id'),
    //         'aff_id' => session('aff'),
    //         'fl_sid' => $fl_sid,
    //         'wauuid' => $wauuid,
    //         'sessid' => $sessid
    //     ];

    //     if (checkdnsrr('true-serv.net', 'A')) {
    //         try {
    //             Log::info("PayVMC data answer: " . json_encode($data));
    //             $response = Http::timeout(10)->post('http://true-serv.net/checkout/order.php', $data);
    //             Log::info("PayVMC answer: " . $response);

    //             if ($response->successful()) {
    //                 $response = json_decode($response, true);

    //                 if ($response['status'] == 'ERROR') {
    //                     return response()->json(['response' => $response], 400);
    //                 } else {
    //                     return response()->json(['response' => $response], 200);
    //                 }
    //             } else {
    //                 // Обработка ответа с ошибкой (4xx или 5xx)
    //                 Log::error("Сервис вернул ошибку: " . $response->status());
    //                 $responseData = ['error' => 'Service returned an error'];
    //             }
    //         } catch (ConnectionException $e) {
    //             Log::error("Ошибка подключения: " . $e->getMessage());
    //         } catch (RequestException $e) {
    //             // Обработка ошибок запроса, таких как таймаут или недоступность
    //             Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
    //             $responseData = ['error' => 'Service unavailable'];
    //         }
    //     }
    // }

    public function send_payvmc_ids(Request $request)
    {
        $this->rememberPayvmcIds(
            $request->input('fl_sid'),
            $request->input('wauuid')
        );

        $orderId = $request->input('order_id') ?: session('order.order_id');

        $result = $this->sendPayvmcIdsFromSession($orderId);

        return response()->json(
            ['response' => $result['response']],
            $result['code']
        );
    }

    public function bonus_card_process(Request $request)
    {
        $request->request->add(['expire_date' => $request->card_month . '/' . $request->card_year]);

        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;
            $api_key    = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $data = [
                'method'             => 'order',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $request->phone),
                'alternative_phone'  => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email'              => e($request->email),
                'alter_email'        => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname'          => e($request->firstname),
                'lastname'           => e($request->lastname),
                'billing_country'    => e($request->billing_country),
                'billing_state'      => e($request->billing_state),
                'billing_city'       => e($request->billing_city),
                'billing_address'    => e($request->billing_address),
                'billing_zip'        => e($request->billing_zip),
                'shipping_country'   => !empty($request->address_match) ? e($request->shipping_country) : e(
                    $request->billing_country
                ),
                'shipping_state'     => !empty($request->address_match) ? e($request->shipping_state) : e(
                    $request->billing_state
                ),
                'shipping_city'      => !empty($request->address_match) ? e($request->shipping_city) : e(
                    $request->billing_city
                ),
                'shipping_address'   => !empty($request->address_match) ? e($request->shipping_address) : e(
                    $request->billing_address
                ),
                'shipping_zip'       => !empty($request->address_match) ? e($request->shipping_zip) : e(
                    $request->billing_zip
                ),
                'payment_type'       => 'bonus_card',
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                        'Accept-Language'
                    ) . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            session(['data' => $data]);

            $order_cache_id = $this->getOrCreateOrderCache($data, $request->email);

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $httpResponse = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Bonus Card answer: " . $httpResponse);

                    if ($httpResponse->successful()) {
                        // Обработка успешного ответа

                        $response = $httpResponse->json();

                        if (!is_array($response)) {
                            $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => 'Invalid service response'
                                ]
                            ], 502);
                        }

                        if ($this->isFinalOrderResponse($response)) {

                            $this->finalizeSuccessfulOrder($order_cache_id, $response);

                        } else {
                            $this->markOrderRetry(
                                $order_cache_id,
                                'Unexpected response: ' . json_encode($response)
                            );
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                       Log::error("Сервис вернул ошибку: " . $httpResponse->status());

                        $this->markOrderRetry(
                            $order_cache_id,
                            'HTTP status: ' . $httpResponse->status()
                        );

                        return response()->json([
                            'response' => [
                                'status' => 'SUCCESS'
                            ]
                        ], 200);
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (RequestException $e) {
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (\Throwable $e) {
                    Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            } else {
                $this->markOrderRetry($order_cache_id, 'DNS unavailable');

                session(['order' => 'error']);

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
        }
    }

    public function gift_card_process(Request $request)
    {
        $request->request->add(['expire_date' => $request->card_month . '/' . $request->card_year]);

        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;
            $api_key    = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $data = [
                'method'             => 'order',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $request->phone),
                'alternative_phone'  => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email'              => e($request->email),
                'alter_email'        => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname'          => e($request->firstname),
                'lastname'           => e($request->lastname),
                'billing_country'    => e($request->billing_country),
                'billing_state'      => e($request->billing_state),
                'billing_city'       => e($request->billing_city),
                'billing_address'    => e($request->billing_address),
                'billing_zip'        => e($request->billing_zip),
                'shipping_country'   => !empty($request->address_match) ? e($request->shipping_country) : e(
                    $request->billing_country
                ),
                'shipping_state'     => !empty($request->address_match) ? e($request->shipping_state) : e(
                    $request->billing_state
                ),
                'shipping_city'      => !empty($request->address_match) ? e($request->shipping_city) : e(
                    $request->billing_city
                ),
                'shipping_address'   => !empty($request->address_match) ? e($request->shipping_address) : e(
                    $request->billing_address
                ),
                'shipping_zip'       => !empty($request->address_match) ? e($request->shipping_zip) : e(
                    $request->billing_zip
                ),
                'payment_type'       => 'gift_card',
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                        'Accept-Language'
                    ) . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            session(['data' => $data]);

            $order_cache_id = $this->getOrCreateOrderCache($data, $request->email);

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $httpResponse = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Gift Card answer: " . $httpResponse);

                    if ($httpResponse->successful()) {
                        // Обработка успешного ответа

                        $response = $httpResponse->json();

                        if (!is_array($response)) {
                            $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => 'Invalid service response'
                                ]
                            ], 502);
                        }

                        if ($this->isFinalOrderResponse($response)) {

                            $this->finalizeSuccessfulOrder($order_cache_id, $response);

                        } else {
                            $this->markOrderRetry(
                                $order_cache_id,
                                'Unexpected response: ' . json_encode($response)
                            );
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $httpResponse->status());

                        $this->markOrderRetry(
                            $order_cache_id,
                            'HTTP status: ' . $httpResponse->status()
                        );

                        return response()->json([
                            'response' => [
                                'status' => 'SUCCESS'
                            ]
                        ], 200);
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (RequestException $e) {
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (\Throwable $e) {
                    Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            } else {
                $this->markOrderRetry($order_cache_id, 'DNS unavailable');

                session(['order' => 'error']);

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
        }
    }

    public function validate_for_wallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {

            $form = session('form');

            session(['form.payment_type' => $form['wallet']]);

            $phone_code = PhoneCodes::where('iso', '=', $form['billing_country'])->first();
            $phone_code = $phone_code->phonecode;

            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $billing_state  = isset($form['billing_state']) ? e($form['billing_state']) : '';
            $shipping_state = isset($form['shipping_state']) ? e($form['shipping_state']) : '';

            $data = [
                'method'             => 'save_order_data',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $form['phone']),
                'alternative_phone'  => !empty($form['alt_phone']) ? e('+' . $phone_code . $form['alt_phone']) : '',
                'email'              => e($form['email']),
                'alter_email'        => !empty($form['alt_email']) ? e($form['alt_email']) : '',
                'firstname'          => e($form['firstname']),
                'lastname'           => e($form['lastname']),
                'billing_country'    => e($form['billing_country']),
                'billing_state'      => $billing_state,
                'billing_city'       => e($form['billing_city']),
                'billing_address'    => e($form['billing_address']),
                'billing_zip'        => e($form['billing_zip']),
                'shipping_country'   => !empty($form['address_match']) ? e($form['shipping_country']) : e(
                    $form['billing_country']
                ),
                'shipping_state'     => !empty($form['address_match']) ? $shipping_state : $billing_state,
                'shipping_city'      => !empty($form['address_match']) ? e($form['shipping_city']) : e(
                    $form['billing_city']
                ),
                'shipping_address'   => !empty($form['address_match']) ? e($form['shipping_address']) : e(
                    $form['billing_address']
                ),
                'shipping_zip'       => !empty($form['address_match']) ? e($form['shipping_zip']) : e($form['billing_zip']),
                'payment_type'       => $form['wallet'],
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent(),
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $response = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        }
    }

    public function wallet_process(Request $request)
    {
        // Validator::extend('credit_card_number', function ($attribute, $value, $parameters, $validator) {
        //     $regex = null;
        //     $type  = 'all';
        //     $ccNum = str_replace(array('-', ' '), '', $value);
        //     if (empty($ccNum) || strlen($ccNum) < 13 || strlen($ccNum) > 19) {
        //         return false;
        //     }

        //     $card_number_checksum = '';

        //     foreach (str_split(strrev((string)$ccNum)) as $i => $d) {
        //         $card_number_checksum .= $i % 2 !== 0 ? $d * 2 : $d;
        //     }
        //     // Check if the card number is valid
        //     if (!(array_sum(str_split($card_number_checksum)) % 10 === 0)) {
        //         return false;
        //     }

        //     if ($regex !== null) {
        //         if (is_string($regex) && preg_match($regex, $ccNum)) {
        //             return true;
        //         }
        //         return false;
        //     }

        //     $cards = array(
        //         'all'  => array(
        //             'amex'     => '/^3[4|7]\\d{13}$/',
        //             // 'bankcard' => '/^56(10\\d\\d|022[1-5])\\d{10}$/',
        //             // 'diners'   => '/^(?:3(0[0-5]|[68]\\d)\\d{11})|(?:5[1-5]\\d{14})$/',
        //             'disc'     => '/^(?:6011|650\\d)\\d{12}$/',
        //             // 'electron' => '/^(?:417500|4917\\d{2}|4913\\d{2})\\d{10}$/',
        //             // 'enroute'  => '/^2(?:014|149)\\d{11}$/',
        //             // 'jcb'      => '/^(3\\d{4}|2100|1800)\\d{11}$/',
        //             // 'maestro'  => '/^(?:5020|6\\d{3})\\d{12}$/',
        //             'mc'       => '/^(5[1-5]\d{14}|222[1-9]\d{12}|22[3-9]\d{13}|2[3-6]\d{14}|27[01]\d{13}|2720\d{12})$/',
        //             // 'solo'     => '/^(6334[5-9][0-9]|6767[0-9]{2})\\d{10}(\\d{2,3})?$/',
        //             // 'switch'   =>
        //             //     '/^(?:49(03(0[2-9]|3[5-9])|11(0[1-2]|7[4-9]|8[1-2])|36[0-9]{2})\\d{10}(\\d{2,3})?)|(?:564182\\d{10}(\\d{2,3})?)|(6(3(33[0-4][0-9])|759[0-9]{2})\\d{10}(\\d{2,3})?)$/',
        //             'visa'     => '/^4\\d{12}(\\d{3})?$/',
        //             // 'voyager'  => '/^8699[0-9]{11}$/'
        //         ),
        //         // 'fast' =>
        //         //     '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$/'
        //     );

        //     if (session('visa_error', false) === true && preg_match($cards['all']['visa'], $ccNum)) {
        //         return false;
        //     }

        //     if (is_array($type)) {
        //         foreach ($type as $value) {
        //             $regex = $cards['all'][strtolower($value)];

        //             if (is_string($regex) && preg_match($regex, $ccNum)) {
        //                 return true;
        //             }
        //         }
        //     } elseif ($type === 'all') {
        //         foreach ($cards['all'] as $value) {
        //             $regex = $value;

        //             if (is_string($regex) && preg_match($regex, $ccNum)) {
        //                 return true;
        //             }
        //         }
        //     } else {
        //         // $regex = $cards['fast'];

        //         // if (is_string($regex) && preg_match($regex, $ccNum)) {
        //         //     return true;
        //         // }
        //     }
        //     return false;
        // });

        // $request->request->add(['expire_date' => $request->card_month . '/' . $request->card_year]);

        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
            // 'card_numb'        => ['required', 'credit_card_number', [
            //     'card_number.credit_card_number' => 'Visa card error.',
            // ]],
            // 'bank_name'        => ['required'],
            // 'expire_date'      => ['required', 'date_format:m/Y', 'after:now'],
            // 'cvc_2'            => ['required', 'min:3', 'max:4']
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;
            $api_key    = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $data = [
                'method'             => 'order',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $request->phone),
                'alternative_phone'  => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email'              => e($request->email),
                'alter_email'        => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname'          => e($request->firstname),
                'lastname'           => e($request->lastname),
                'billing_country'    => e($request->billing_country),
                'billing_state'      => e($request->billing_state),
                'billing_city'       => e($request->billing_city),
                'billing_address'    => e($request->billing_address),
                'billing_zip'        => e($request->billing_zip),
                'shipping_country'   => !empty($request->address_match) ? e($request->shipping_country) : e(
                    $request->billing_country
                ),
                'shipping_state'     => !empty($request->address_match) ? e($request->shipping_state) : e(
                    $request->billing_state
                ),
                'shipping_city'      => !empty($request->address_match) ? e($request->shipping_city) : e(
                    $request->billing_city
                ),
                'shipping_address'   => !empty($request->address_match) ? e($request->shipping_address) : e(
                    $request->billing_address
                ),
                'shipping_zip'       => !empty($request->address_match) ? e($request->shipping_zip) : e(
                    $request->billing_zip
                ),
                'payment_type'       => $request->wallet,
                // 'card_holder'        => e($request->firstname . ' ' . $request->lastname),
                // 'card_number'        => e($request->card_numb),
                // 'bank_name'          => e($request->bank_name),
                // 'card_month'         => e($request->card_month),
                // 'card_year'          => e($request->card_year),
                // 'card_cvv'           => e($request->cvc_2),
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                        'Accept-Language'
                    ) . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            session(['data' => $data]);

            $order_cache_id = $this->getOrCreateOrderCache($data, $request->email);

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $httpResponse = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Wallet answer: " . $httpResponse);

                    if ($httpResponse->successful()) {
                        // Обработка успешного ответа

                        $response = $httpResponse->json();

                        if (!is_array($response)) {
                            $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => 'Invalid service response'
                                ]
                            ], 502);
                        }

                        // if ($this->hasVisaError($response)) {
                        //     DB::table('order_cache')
                        //         ->where('id', $order_cache_id)
                        //         ->delete();

                        //     $this->sendPayvmcIdsFromSession($response['order_id'] ?? null);

                        //     session(['visa_error' => true]);
                        //     session(['form.card_numb' => '']);
                        //     session(['form.bank_name' => '']);
                        //     session(['form.card_month' => '']);
                        //     session(['form.card_year' => '']);
                        //     session(['form.cvc_2' => '']);

                        //     return response()->json([
                        //         'response' => [
                        //             'status' => 'ERROR',
                        //             'message' => __('text.visa_error_text'),
                        //             'visa_error' => true,
                        //             'html' => $this->checkout(),
                        //         ]
                        //     ], 200);
                        // }

                        $message = $response['message'] ?? null;

                        $hasRiskCheckFailed = false;

                        if (is_array($message)) {
                            $hasRiskCheckFailed = in_array('risk_check_failed', $message, true);
                        } elseif (is_string($message)) {
                            $hasRiskCheckFailed = str_contains($message, 'risk_check_failed');
                        }

                        $status = strtolower((string) ($response['status'] ?? ''));

                        if ($status === 'error' && $hasRiskCheckFailed) {

                            DB::table('order_cache')
                                ->where('id', $order_cache_id)
                                ->delete();

                            session(['wallet_available' => false]);
                            session(['form.payment_type' => 'mastercard']);

                            return response()->json([
                                'response' => [
                                    'status' => 'risk_check',
                                    'message' => __('text.risk_check_failed'),
                                    'html' => $this->checkout(),
                                ]
                            ], 200);
                        }

                        if ($this->isFinalOrderResponse($response)) {
                            $this->finalizeSuccessfulOrder($order_cache_id, $response);
                            session(['wallet_available' => true]);

                            return response()->json([
                                'response' => [
                                    'status' => 'SUCCESS',
                                    'url' => $response['url'] ?? null,
                                ]
                            ], 200);
                        } else {
                            $this->markOrderRetry(
                                $order_cache_id,
                                'Unexpected response: ' . json_encode($response)
                            );
                        }
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $httpResponse->status());

                        $this->markOrderRetry(
                            $order_cache_id,
                            'HTTP status: ' . $httpResponse->status()
                        );

                        return response()->json([
                            'response' => [
                                'status' => 'SUCCESS'
                            ]
                        ], 200);
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (RequestException $e) {
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (\Throwable $e) {
                    Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            } else {
                $this->markOrderRetry($order_cache_id, 'DNS unavailable');

                session(['order' => 'error']);

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
        }
    }

    public function open_banking_process(Request $request)
    {
        $request->request->add(['expire_date' => $request->card_month . '/' . $request->card_year]);

        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            $products = [];
            $sessid   = '';

            foreach (session('cart') as $product) {
                $products[$product['pack_id']] = [
                    'qty'            => $product['q'],
                    'price'          => $product['price'],
                    'is_ed_category' => false
                ];

                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : SessionHelper::getSessionId($request);
            }

            // if (session('cart_option.bonus_id') != 0) {
            //     $products[session('cart_option.bonus_id')] = [
            //         'qty'            => 1,
            //         'price'          => session('cart_option.bonus_price'),
            //         'is_ed_category' => false
            //     ];
            // }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;
            $api_key    = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $data = [
                'method'             => 'order',
                'api_key'            => $api_key->key_data,
                'phone'              => e('+' . $phone_code . $request->phone),
                'alternative_phone'  => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email'              => e($request->email),
                'alter_email'        => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname'          => e($request->firstname),
                'lastname'           => e($request->lastname),
                'billing_country'    => e($request->billing_country),
                'billing_state'      => e($request->billing_state),
                'billing_city'       => e($request->billing_city),
                'billing_address'    => e($request->billing_address),
                'billing_zip'        => e($request->billing_zip),
                'shipping_country'   => !empty($request->address_match) ? e($request->shipping_country) : e(
                    $request->billing_country
                ),
                'shipping_state'     => !empty($request->address_match) ? e($request->shipping_state) : e(
                    $request->billing_state
                ),
                'shipping_city'      => !empty($request->address_match) ? e($request->shipping_city) : e(
                    $request->billing_city
                ),
                'shipping_address'   => !empty($request->address_match) ? e($request->shipping_address) : e(
                    $request->billing_address
                ),
                'shipping_zip'       => !empty($request->address_match) ? e($request->shipping_zip) : e(
                    $request->billing_zip
                ),
                'payment_type'       => 'open_banking',
                'is_revolut'         => $request->is_revolut ?? 0,
                'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                    'cf-connecting-ip'
                ) : request()->ip(),
                'aff'                => session('aff', 0),
                'ref'                => session('referer', ''),
                'refc'               => session('refc', ''),
                'keyword'            => session('keyword', ''),
                'domain_from'        => request()->getHost(),
                'total'              => session('total.checkout_total'),
                'shipping'           => session('cart_option.shipping'),
                'products'           => $products_str,
                'saff'               => session('saff', ''),
                'language'           => App::currentLocale(),
                'currency'           => session('currency'),
                'user_agent'         => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header(
                        'Accept-Language'
                    ) . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint'        => '',
                'product_total'      => session('total.product_total'),
                'customer_id'        => '',
                'reorder'            => 0,
                'reorder_discount'   => 0,
                'shipping_price'     => session('total.shipping_total'),
                'insurance'          => session('total.insurance'),
                'secret_package'     => session('total.secret_package'),
                'store_skin'         => config('app.design'),
                'recurring_period'   => 0,
                'bonus'              => session('cart_option.bonus_id', 0),
                'theme'              => 13,
                'sessid'             => $sessid,
                'browser_details' => [
                    'browser_accept_header' => $_SERVER['HTTP_ACCEPT'] ?? '',
                    'browser_color_depth' => $request->browser_details['browser_color_depth'] ?? '',
                    'browser_language' => $request->browser_details['browser_language'] ?? '',
                    'browser_screen_height' => $request->browser_details['browser_screen_height'] ?? '',
                    'browser_screen_width' => $request->browser_details['browser_screen_width'] ?? '',
                    'browser_timezone' => $request->browser_details['browser_timezone'] ?? '',
                    'browser_ip' => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                    'browser_user_agent' => $request->userAgent(),
                    'browser_java_enable' => $request->browser_details['browser_java_enable'] ?? false,
                    'window_height' => $request->browser_details['window_height'] ?? '',
                    'window_width' => $request->browser_details['window_width'] ?? '',
                ],
                'coupon' => session('checked_bonus', 'discount') == 'discount' ? session('coupon.coupon', '') : '',
                'coupon_discount' => session('checked_bonus', 'discount') == 'discount' ? session('total.coupon_discount', 0) : 0,
                'gift_card_code' => session('checked_bonus', 'discount') == 'gift_card' ? session('gift_card.gift_card_code', '') : '',
                'gift_card_discount' => session('checked_bonus', 'discount') == 'gift_card' ? session('total.gift_card_discount', 0) : 0,
                'bonus_card_number' => session('checked_bonus', 'discount') == 'bonus_card' ? session('bonus_card.card_number', '') : '',
                'bonus_card_discount' => session('checked_bonus', 'discount') == 'bonus_card' ? session('total.bonus_card_discount', 0) : 0,
                'is_pwa' => session('is_pwa', 0),
            ];

            session(['data' => $data]);

            $order_cache_id = $this->getOrCreateOrderCache($data, $request->email);

            if (checkdnsrr('true-serv.net', 'A')) {
                try {
                    $httpResponse = Http::timeout(30)->post('http://true-serv.net/checkout/order.php', $data);
                    Log::info("Open Banking answer: " . $httpResponse);

                    if ($httpResponse->successful()) {
                        // Обработка успешного ответа

                        $response = $httpResponse->json();

                        if (!is_array($response)) {
                            $this->markOrderRetry($order_cache_id, 'Invalid JSON response');

                            return response()->json([
                                'response' => [
                                    'status' => 'ERROR',
                                    'message' => 'Invalid service response'
                                ]
                            ], 502);
                        }

                        $message = $response['message'] ?? null;

                        $hasRiskCheckFailed = false;

                        if (is_array($message)) {
                            $hasRiskCheckFailed = in_array('risk_check_failed', $message, true);
                        } elseif (is_string($message)) {
                            $hasRiskCheckFailed = str_contains($message, 'risk_check_failed');
                        }

                        $status = strtolower((string) ($response['status'] ?? ''));

                        if ($status === 'error' && $hasRiskCheckFailed) {

                            DB::table('order_cache')
                                ->where('id', $order_cache_id)
                                ->delete();

                            session(['open_banking_available' => false]);
                            session(['form.payment_type' => 'mastercard']);

                            return response()->json([
                                'response' => [
                                    'status' => 'risk_check',
                                    'message' => __('text.risk_check_failed'),
                                    'html' => $this->checkout(),
                                ]
                            ], 200);
                        }

                        if ($this->isFinalOrderResponse($response)) {
                            $this->finalizeSuccessfulOrder($order_cache_id, $response);
                            session(['open_banking_available' => true]);
                        } else {
                            $this->markOrderRetry(
                                $order_cache_id,
                                'Unexpected response: ' . json_encode($response)
                            );
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                       Log::error("Сервис вернул ошибку: " . $httpResponse->status());

                        $this->markOrderRetry(
                            $order_cache_id,
                            'HTTP status: ' . $httpResponse->status()
                        );

                        return response()->json([
                            'response' => [
                                'status' => 'SUCCESS'
                            ]
                        ], 200);
                    }
                } catch (ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (RequestException $e) {
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);

                } catch (\Throwable $e) {
                    Log::error("Неожиданная ошибка отправки заказа: " . $e->getMessage());

                    $this->markOrderRetry($order_cache_id, $e->getMessage());

                    return response()->json([
                        'response' => [
                            'status' => 'SUCCESS'
                        ]
                    ], 200);
                }
            } else {
                $this->markOrderRetry($order_cache_id, 'DNS unavailable');

                session(['order' => 'error']);

                return response()->json([
                    'response' => [
                        'status' => 'SUCCESS'
                    ]
                ], 200);
            }
        }
    }

    public function recalculation(Request $request)
    {
        $form = $request->all();

        $validator = Validator::make($request->all(), [
            'phone'            => ['required', 'min:5', 'max:16'],
            'email'            => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email'        => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone'        => ['nullable', 'min:5', 'max:16'],
            'firstname'        => ['required', 'max:255'],
            'lastname'         => ['required', 'max:255'],
            'billing_country'  => ['required', 'max:2'],
            'billing_city'     => ['required', 'max:255'],
            'billing_address'  => ['required', 'max:255'],
            'billing_zip'      => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city'    => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip'     => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        } else {
            session(['bonus_checkout_payment' => $form['bonus_checkout_payment']]);
            session(['form.payment_type' => $form['bonus_checkout_payment']]);

            return $this->checkout();
        }
    }

    public function forget_bonuses(Request $request)
    {
        $witch_forget = $request->witch_forget;

        if ($witch_forget) {
            if ($witch_forget == 'discount') {
                session()->forget('coupon');
            } else {
                session()->forget($witch_forget);
            }

            session()->forget('crypto');
            // session(['form.payment_type' => 'card']);
            session(['form.payment_type' => 'mastercard']);
        }

        return $this->checkout();
    }

    private function retryUnsentOrders(): void
    {
        if (!checkdnsrr('true-serv.net', 'A')) {
            return;
        }

        $now = now();

        $unsentOrders = DB::table('order_cache')
            ->where('is_send', 0)
            ->where(function ($query) use ($now) {
                $query->whereNull('next_attempt_at')
                    ->orWhere('next_attempt_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('locked_at')
                    ->orWhere('locked_at', '<=', $now->copy()->subMinutes(10));
            })
            ->orderBy('id')
            ->limit(10)
            ->get();

        foreach ($unsentOrders as $order) {
            $claimed = DB::table('order_cache')
                ->where('id', $order->id)
                ->where('is_send', 0)
                ->where(function ($query) use ($now) {
                    $query->whereNull('next_attempt_at')
                        ->orWhere('next_attempt_at', '<=', $now);
                })
                ->where(function ($query) use ($now) {
                    $query->whereNull('locked_at')
                        ->orWhere('locked_at', '<=', $now->copy()->subMinutes(10));
                })
                ->update([
                    'locked_at' => now(),
                ]);

            if ($claimed !== 1) {
                continue;
            }

            $this->sendCachedOrder($order);
        }
    }

    private function sendCachedOrder(object $order): void
    {
        try {
            $payload = json_decode($order->message, true);

            if (!is_array($payload)) {
                $this->markOrderRetry($order->id, 'Invalid JSON in order_cache.message');
                return;
            }

            $httpResponse = Http::timeout(30)->post(
                'http://true-serv.net/checkout/order.php',
                $payload
            );

            Log::info("Retry order answer: " . $httpResponse);

            if (!$httpResponse->successful()) {
                Log::error("Сервис вернул ошибку: " . $httpResponse->status());

                $this->markOrderRetry(
                    $order->id,
                    'HTTP status: ' . $httpResponse->status()
                );

                return;
            }

            $response = $httpResponse->json();

            if (!is_array($response)) {
                $this->markOrderRetry($order->id, 'Invalid JSON response from service');
                return;
            }

            if ($this->isFinalOrderResponse($response)) {
                DB::table('order_cache')
                    ->where('id', $order->id)
                    ->delete();

                return;
            }

            $this->markOrderRetry(
                $order->id,
                'Unexpected response: ' . json_encode($response)
            );

        } catch (ConnectionException $e) {
            Log::error("Ошибка подключения: " . $e->getMessage());

            // $this->markOrderRetry($order->id, $e->getMessage());

        } catch (RequestException $e) {
            Log::error("Ошибка HTTP-запроса: " . $e->getMessage());

            // $this->markOrderRetry($order->id, $e->getMessage());

        } catch (\Throwable $e) {
            Log::error("Неожиданная ошибка retry order: " . $e->getMessage());

            // $this->markOrderRetry($order->id, $e->getMessage());
        }
    }

    private function isFinalOrderResponse(array $response): bool
    {
        $status = strtolower((string) ($response['status'] ?? ''));

        $message = isset($response['message'])
            ? json_encode($response['message'])
            : '';

        return $status === 'success'
            || (
                $status === 'error'
                && (
                    str_contains($message, 'repeat_order')
                    || str_contains($message, 'risk_check_failed')
                )
            );
    }

    private function hasVisaError(array $response): bool
    {
        return isset($response['paymethod_error'])
            && filter_var($response['paymethod_error'], FILTER_VALIDATE_BOOLEAN) === true;
    }

    private function markOrderRetry(int $orderId, ?string $error = null): void
    {
        $order = DB::table('order_cache')
            ->where('id', $orderId)
            ->first(['attempts']);

        if (!$order) {
            return;
        }

        $attempts = (int) $order->attempts + 1;
        $delayMinutes = $this->getRetryDelayMinutes($attempts);

        DB::table('order_cache')
            ->where('id', $orderId)
            ->update([
                'attempts' => $attempts,
                'last_attempt_at' => now(),
                'next_attempt_at' => now()->addMinutes($delayMinutes),
                'last_error' => $error,
                'locked_at' => null,
            ]);
    }

    private function getRetryDelayMinutes(int $attempts): int
    {
        $schedule = [
            1 => 5,
            2 => 10,
            3 => 30,
            4 => 60,
            5 => 120,
            6 => 240,
            7 => 480,
            8 => 1440,
        ];

        return $schedule[$attempts] ?? 1440;
    }

    private function getOrCreateOrderCache(array $data, string $email): int
    {
        $existingOrder = DB::table('order_cache')
            ->where('is_send', 0)
            ->where('message', 'LIKE', '%' . $email . '%')
            ->first();

        if ($existingOrder) {
            return $existingOrder->id;
        }

        $dataForCache = $data;

        return DB::table('order_cache')->insertGetId([
            'message' => json_encode($dataForCache, JSON_UNESCAPED_UNICODE),
            'is_send' => 0,
            'attempts' => 0,
            'next_attempt_at' => null,
            'last_attempt_at' => null,
            'last_error' => null,
            'locked_at' => null,
        ]);
    }

    private function ensureOrderCacheRetryColumns(): void
    {
        if (!Schema::hasTable('order_cache')) {
            Log::error('Table order_cache does not exist');
            return;
        }

        $missing = [
            'attempts'        => !Schema::hasColumn('order_cache', 'attempts'),
            'next_attempt_at' => !Schema::hasColumn('order_cache', 'next_attempt_at'),
            'last_attempt_at' => !Schema::hasColumn('order_cache', 'last_attempt_at'),
            'last_error'      => !Schema::hasColumn('order_cache', 'last_error'),
            'locked_at'       => !Schema::hasColumn('order_cache', 'locked_at'),
        ];

        if (!in_array(true, $missing, true)) {
            return;
        }

        try {
            Schema::table('order_cache', function (Blueprint $table) use ($missing) {
                if ($missing['attempts']) {
                    $table->unsignedInteger('attempts')->default(0);
                }

                if ($missing['next_attempt_at']) {
                    $table->dateTime('next_attempt_at')->nullable();
                }

                if ($missing['last_attempt_at']) {
                    $table->dateTime('last_attempt_at')->nullable();
                }

                if ($missing['last_error']) {
                    $table->text('last_error')->nullable();
                }

                if ($missing['locked_at']) {
                    $table->dateTime('locked_at')->nullable();
                }
            });
        } catch (\Throwable $e) {
            Log::error('Cannot alter order_cache table: ' . $e->getMessage());
        }

        try {
            DB::statement("
                CREATE INDEX idx_order_cache_retry
                ON order_cache (is_send, next_attempt_at, locked_at)
            ");
        } catch (\Throwable $e) {
            // Если индекс уже существует — это не критично.
            Log::info('order_cache retry index was not created: ' . $e->getMessage());
        }
    }
    private function ensureRequiredShopKeys(): void
    {
        try {
            $requiredKeys = [
                'bonus_card' => 'dfv3j8vhutiy54734svfsevf',
                'local_payment' => 'c0c840306bjd0t1dad7c039d2633b64d',
            ];

            foreach ($requiredKeys as $nameKey => $keyData) {
                $exists = DB::table('shop_keys')
                    ->where('name_key', $nameKey)
                    ->exists();

                if (!$exists) {
                    DB::table('shop_keys')->insert([
                        'name_key' => $nameKey,
                        'key_data' => $keyData,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Cannot check or insert shop_keys: ' . $e->getMessage());
        }
    }

    private function rememberPayvmcIds($flSid, $wauuid): void
    {
        $flSid = is_string($flSid) ? trim($flSid) : '';
        $wauuid = is_string($wauuid) ? trim($wauuid) : '';

        if ($flSid !== '') {
            // Session::put('payvmc.fl_sid', $flSid);
            session(['payvmc.fl_sid' => $flSid]);
        }

        if ($wauuid !== '') {
            // Session::put('payvmc.wauuid', $wauuid);
            session(['payvmc.wauuid' => $wauuid]);
        }
    }

    private function finalizeSuccessfulOrder(int $orderCacheId, array $response): void
    {
        DB::table('order_cache')
            ->where('id', $orderCacheId)
            ->delete();

        session(['order' => $response]);

        $successOrderPage = [
            'order' => $response,
            'firstname' => session('form.firstname', ''),
            'lastname' => session('form.lastname', ''),
            'checkout_total' => session('total.checkout_total', 0),
        ];

        session(['success_order_page' => $successOrderPage]);

        Cookie::queue(
            Cookie::make(
                'success_order_page',
                json_encode($successOrderPage),
                60 * 24
            )
        );

        // $this->sendPayvmcIdsFromSession();
    }

    private function sendPayvmcIdsFromSession($orderId = null): array
    {
        $flSid = session('payvmc.fl_sid');
        $wauuid = session('payvmc.wauuid');

        $orderId = $orderId ?: session('order.order_id');

        if (empty($flSid) || empty($wauuid)) {
            Log::warning('PayVMC ids are empty', [
                'fl_sid' => $flSid,
                'wauuid' => $wauuid,
                'order_id' => $orderId,
            ]);

            return [
                'code' => 422,
                'response' => [
                    'status' => 'ERROR',
                    'message' => 'PayVMC ids are empty',
                ],
            ];
        }

        if (empty($orderId)) {
            return [
                'code' => 200,
                'response' => [
                    'status' => 'SAVED',
                    'message' => 'PayVMC ids saved, order_id is empty',
                ],
            ];
        }

        $api_key = DB::table('shop_keys')
            ->where('name_key', '=', 'api_key')
            ->get('key_data')
            ->toArray()[0];

        $data = [
            'method'   => 'send_payvmc_ids',
            'api_key'  => $api_key->key_data,
            'order_id' => $orderId,
            'aff_id'   => session('aff'),
            'fl_sid'   => $flSid,
            'wauuid'   => $wauuid,
            'sessid'   => session()->getId(),
        ];

        if (checkdnsrr('true-serv.net', 'A')) {
            try {
                Log::info("PayVMC data answer: " . json_encode($data));

                $response = Http::timeout(30)
                    ->post('http://true-serv.net/checkout/order.php', $data);

                Log::info("PayVMC answer: " . $response);

                if ($response->successful()) {
                    $responseData = json_decode($response, true);

                    if (isset($responseData['status']) && $responseData['status'] == 'ERROR') {
                        return [
                            'code' => 400,
                            'response' => $responseData,
                        ];
                    }

                    Session::forget('payvmc');

                    return [
                        'code' => 200,
                        'response' => $responseData,
                    ];
                }

                Log::error("PayVMC service returned error: " . $response->status());

            } catch (ConnectionException $e) {
                Log::error("PayVMC connection error: " . $e->getMessage());
            } catch (RequestException $e) {
                Log::error("PayVMC request error: " . $e->getMessage());
            } catch (\Throwable $e) {
                Log::error("PayVMC unexpected error: " . $e->getMessage());
            }
        }

        return [
            'code' => 500,
            'response' => [
                'status' => 'ERROR',
                'message' => 'PayVMC request failed',
            ],
        ];
    }

    public function new_order()
    {
        Session::forget('success_order_page');

        Cookie::queue(Cookie::forget('success_order_page'));

        return redirect()->route('checkout.index');
    }
}