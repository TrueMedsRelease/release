<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function index()
    {
        if (empty(session('cart'))) {
            return redirect(route('home.index'));
        }

        if (session('crypto')) {
            Session::forget('crypto');
        }

        $statisticPromise = StatisticService::SendStatistic('checkout');
        StatisticService::SendCheckout();

        $unsent_order = DB::select("SELECT * FROM order_cache WHERE is_send = 0");
        if (count($unsent_order) > 0) {
            foreach ($unsent_order as $order) {
                if (checkdnsrr('true-services.net', 'A')) {
                    try {
                        $response = Http::timeout(3)->post(
                            'http://true-services.net/checkout/order.php',
                            json_decode($order->message, true)
                        );

                        if ($response->successful()) {
                            // Обработка успешного ответа

                            $response = json_decode($response, true);

                            if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && str_contains(
                                        json_encode($response['message']),
                                        'repeat_order'
                                    ))) {
                                DB::delete("DELETE FROM order_cache WHERE `id` = {$order->id}");
                            }
                        } else {
                            // Обработка ответа с ошибкой (4xx или 5xx)
                            Log::error("Сервис вернул ошибку: " . $response->status());
                            $responseData = ['error' => 'Service returned an error'];
                        }
                    } catch (\Illuminate\Http\Client\ConnectionException $e) {
                        Log::error("Ошибка подключения: " . $e->getMessage());
                    } catch (\Illuminate\Http\Client\RequestException $e) {
                        // Обработка ошибок запроса, таких как таймаут или недоступность
                        Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                        $responseData = ['error' => 'Service unavailable'];
                    }
                }
            }
        }

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'checkout'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $design = session('design') ? session('design') : config('app.design');

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        $paypal_limit = 'none';
        $api_key      = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $message = [
            'method'  => 'get_paypal_limit',
            'api_key' => $api_key->key_data,
        ];

        if (env("APP_PAYPAL_ON", false) && checkdnsrr('true-services.net', 'A')) {
            try {
                $response = Http::timeout(5)->post('http://true-services.net/checkout/order.php', $message);
                $response = json_decode($response, true);

                if ($response['status'] == 'success') {
                    $paypal_limit = $response['limit'];
                    session(['paypal_limit' => $paypal_limit]);
                } else {
                    session(['paypal_limit' => $paypal_limit]);
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (\Illuminate\Http\Client\RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }

        return view('checkout', [
            'pixel'    => $pixel,
            'Language' => Language::class,
            'Currency' => Currency::class,
        ]);
    }

    public function checkout()
    {
        if (empty(session('cart'))) {
            return redirect(route('home.index'));
        }

        $language_id = isset(Language::$languages[App::currentLocale()]) ? Language::$languages[App::currentLocale()] : Language::$languages['en'];
        $design      = session('design') ? session('design') : config('app.design');
        $desc        = ProductServices::GetProductDesc($language_id);
        $products    = session('cart');

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

        $cart_option = session('cart_option');

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

        if (session('cart_option')['bonus_id'] != 0) {
            $bonus = ProductServices::GetBonuses(session('cart_option')['bonus_id']);
        } else {
            $bonus = '';
        }

        $phone_codes = PhoneCodes::all()->toArray();
        $countries   = CountryInfoCache::all()->toArray();

        Cart::update_cart_total();

        if (!empty(session('form')['phone']) && str_contains(session('form')['phone'], '+')) {
            for ($i = 0; $i < count($phone_codes); $i++) {
                if ($phone_codes[$i]['iso'] == session('form')['billing_country']) {
                    Session::put('form.phone_code', $phone_codes[$i]['iso']);
                    $code = '+' . $phone_codes[$i]['phonecode'];
                    Session::put('form.phone', str_replace($code, '', session('form')['phone']));
                }
            }
        }

        $service_enable = true;
        if (!checkdnsrr('true-services.net', 'A')) {
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
        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function insurance(Request $request)
    {
        $cart_option = session('cart_option');
        if ($request->val == 0) {
            $cart_option['insurance'] = false;
        } else {
            $cart_option['insurance'] = true;
        }

        session(['cart_option' => $cart_option]);
        session(['form' => $request->all()]);

        Cart::update_cart_total();

        return $this->checkout();
    }

    public function secret_package(Request $request)
    {
        $cart_option = session('cart_option');
        if ($cart_option['secret_package']) {
            $cart_option['secret_package'] = false;
        } else {
            $cart_option['secret_package'] = true;
        }

        session(['cart_option' => $cart_option]);
        session(['form' => $request->all()]);

        return $this->checkout();
    }

    public function change_shipping(Request $request)
    {
        $shipping_name  = $request->shipping_name;
        $shipping_price = $request->shipping_price;

        $option                   = session('cart_option');
        $option['shipping']       = $shipping_name;
        $option['shipping_price'] = $shipping_price;

        session(['cart_option' => $option]);
        session(['form' => $request->all()]);

        return $this->checkout();
    }

    public function change_country(Request $request)
    {
        Session::put('form.billing_country', $request->billing_country);

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

        if (checkdnsrr('true-services.net', 'A')) {
            try {
                $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа

                    $response = json_decode($response, true);

                    if ($response['status'] == 'success') {
                        if ($response['coupon']['type'] == 'coupon') {
                            $result['coupon']  = $coupon;
                            $result['percent'] = $response['coupon']['percent'];
                            $result['type']    = $response['coupon']['type'];

                            session(['coupon' => $result]);
                        }
                    } else {
                        session()->forget('coupon');
                        session()->forget('coupon_get');
                    }
                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (\Illuminate\Http\Client\RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }

        return $this->checkout();
    }

    public function auth(Request $request)
    {
        $email   = $request->email;
        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $data = [
            'method'  => 'auth',
            'api_key' => $api_key->key_data,
            'email'   => $email
        ];

        if (checkdnsrr('true-services.net', 'A')) {
            try {
                $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

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
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (\Illuminate\Http\Client\RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }
    }

    public function order(Request $request)
    {
        Validator::extend('credit_card_number', function ($attribute, $value, $parameters, $validator) {
            $regex = null;
            $type  = 'all';
            $ccNum = str_replace(array('-', ' '), '', $value);
            if (empty($ccNum) || strlen($ccNum) < 13 || strlen($ccNum) > 19) {
                return false;
            }

            $card_number_checksum = '';

            foreach (str_split(strrev((string)$ccNum)) as $i => $d) {
                $card_number_checksum .= $i % 2 !== 0 ? $d * 2 : $d;
            }
            // Check if the card number is valid
            if (!(array_sum(str_split($card_number_checksum)) % 10 === 0)) {
                return false;
            }

            if ($regex !== null) {
                if (is_string($regex) && preg_match($regex, $ccNum)) {
                    return true;
                }
                return false;
            }

            $cards = array(
                'all'  => array(
                    'amex'     => '/^3[4|7]\\d{13}$/',
                    'bankcard' => '/^56(10\\d\\d|022[1-5])\\d{10}$/',
                    'diners'   => '/^(?:3(0[0-5]|[68]\\d)\\d{11})|(?:5[1-5]\\d{14})$/',
                    'disc'     => '/^(?:6011|650\\d)\\d{12}$/',
                    'electron' => '/^(?:417500|4917\\d{2}|4913\\d{2})\\d{10}$/',
                    'enroute'  => '/^2(?:014|149)\\d{11}$/',
                    'jcb'      => '/^(3\\d{4}|2100|1800)\\d{11}$/',
                    'maestro'  => '/^(?:5020|6\\d{3})\\d{12}$/',
                    'mc'       => '/^5[1-5]\\d{14}$/',
                    'solo'     => '/^(6334[5-9][0-9]|6767[0-9]{2})\\d{10}(\\d{2,3})?$/',
                    'switch'   =>
                        '/^(?:49(03(0[2-9]|3[5-9])|11(0[1-2]|7[4-9]|8[1-2])|36[0-9]{2})\\d{10}(\\d{2,3})?)|(?:564182\\d{10}(\\d{2,3})?)|(6(3(33[0-4][0-9])|759[0-9]{2})\\d{10}(\\d{2,3})?)$/',
                    'visa'     => '/^4\\d{12}(\\d{3})?$/',
                    'voyager'  => '/^8699[0-9]{11}$/'
                ),
                'fast' =>
                    '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$/'
            );

            if (is_array($type)) {
                foreach ($type as $value) {
                    $regex = $cards['all'][strtolower($value)];

                    if (is_string($regex) && preg_match($regex, $ccNum)) {
                        return true;
                    }
                }
            } elseif ($type === 'all') {
                foreach ($cards['all'] as $value) {
                    $regex = $value;

                    if (is_string($regex) && preg_match($regex, $ccNum)) {
                        return true;
                    }
                }
            } else {
                $regex = $cards['fast'];

                if (is_string($regex) && preg_match($regex, $ccNum)) {
                    return true;
                }
            }
            return false;
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
            'card_numb'        => ['exclude_unless:payment_type,card', 'required', 'credit_card_number'],
            'bank_name'        => ['exclude_unless:payment_type,card', 'required'],
            'expire_date'      => ['exclude_unless:payment_type,card', 'required', 'date_format:m/Y', 'after:now'],
            'cvc_2'            => ['exclude_unless:payment_type,card', 'required', 'min:3', 'max:4']
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
                $sessid                        = !empty($product['cart_id']) ? $product['cart_id'] : '';
            }

            if (session('cart_option.bonus_id') != 0) {
                $products[session('cart_option.bonus_id')] = [
                    'qty'            => 1,
                    'price'          => session('cart_option.bonus_price'),
                    'is_ed_category' => false
                ];
            }

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
                'payment_type'       => e($request->payment_type),
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
                'coupon'             => session('coupon.coupon', ''),
                'bonus'              => '',
                'gift_card_code'     => '', //session('gift_card.gift_card_code', ''),
                'gift_card_discount' => 0, //session('total.coupon_discount', 0),
                'theme'              => 13,
                'coupon_discount'    => session('total.coupon_discount'),
                'sessid'             => $sessid
            ];

            session(['data' => $data]);

            $email             = e($request->email);
            $check_order_cache = DB::select("SELECT * FROM order_cache WHERE `message` LIKE '%$email%'");
            if (count($check_order_cache) == 0) {
                $data_for_cache             = $data;
                $data_for_cache['products'] = addslashes($data_for_cache['products']);
                $order_cache_id             = DB::table('order_cache')->insertGetId([
                    'message' => json_encode($data_for_cache),
                    'is_send' => 0
                ]);
            } else {
                $order_cache_id = $check_order_cache[0]->id;
            }


            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(10)->post('http://true-services.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                        $response = json_decode($response, true);

                        if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && str_contains(
                                    json_encode($response['message']),
                                    'repeat_order'
                                ))) {
                            DB::delete("DELETE FROM order_cache WHERE `id` = $order_cache_id");
                            session(['order' => $response]);
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            } else {
                session(['order' => 'error']);
                return response()->json(['response' => ['status' => 'SUCCESS']], 200);
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
                $sessid                        = !empty($product['cart_id']) ? $product['cart_id'] : '';
            }

            if (session('cart_option.bonus_id') != 0) {
                $products[session('cart_option.bonus_id')] = [
                    'qty'            => 1,
                    'price'          => session('cart_option.bonus_price'),
                    'is_ed_category' => false
                ];
            }

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
                'coupon'             => session('coupon.coupon', ''),
                'bonus'              => '',
                'gift_card_code'     => '', //session('gift_card.gift_card_code', ''),
                'gift_card_discount' => 0, //session('total.coupon_discount', 0),
                'theme'              => 13,
                'coupon_discount'    => session('total.coupon_discount'),
                'sessid'             => $sessid
            ];

            session(['data' => $data]);

            $email             = e($request->email);
            $check_order_cache = DB::select("SELECT * FROM order_cache WHERE `message` LIKE '%$email%'");
            if (count($check_order_cache) == 0) {
                $data_for_cache             = $data;
                $data_for_cache['products'] = addslashes($data_for_cache['products']);
                $order_cache_id             = DB::table('order_cache')->insertGetId([
                    'message' => json_encode($data_for_cache),
                    'is_send' => 0
                ]);
            } else {
                $order_cache_id = $check_order_cache[0]->id;
            }

            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(10)->post('http://true-services.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                        $response = json_decode($response, true);

                        if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && str_contains(
                                    json_encode($response['message']),
                                    'repeat_order'
                                ))) {
                            DB::delete("DELETE FROM order_cache WHERE `id` = $order_cache_id");
                            session(['order' => $response]);
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
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

        if (checkdnsrr('true-services.net', 'A')) {
            try {
                $response = Http::timeout(10)->post('http://true-services.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа

                    $response = json_decode($response, true);

                    if (isset($response['status']) && $response['status'] == 'error') {
                        return response()->json(json_encode(['status' => 'error', 'text' => 'Service unavailable']));
                    } else {
                        $response['crypto_total'] = Currency::$prefix[session('currency')] . round(session('total.checkout_total') * 0.85 * session('currency_c', 1), 2);
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
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (\Illuminate\Http\Client\RequestException $e) {
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
            $sessid                        = !empty($product['cart_id']) ? $product['cart_id'] : '';
        }

        if (session('cart_option.bonus_id') != 0) {
            $products[session('cart_option.bonus_id')] = [
                'qty'            => 1,
                'price'          => session('cart_option.bonus_price'),
                'is_ed_category' => false
            ];
        }

        $products_str = json_encode($products);

        $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

        $billing_state = isset($form['billing_state']) ? e($form['billing_state']) : '';
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
            'shipping_country'   => !empty($form['address_match']) ? e($form['shipping_country']) : e($form['billing_country']),
            'shipping_state'     => !empty($form['address_match']) ? $shipping_state : $billing_state,
            'shipping_city'      => !empty($form['address_match']) ? e($form['shipping_city']) : e($form['billing_city']),
            'shipping_address'   => !empty($form['address_match']) ? e($form['shipping_address']) : e($form['billing_address']),
            'shipping_zip'       => !empty($form['address_match']) ? e($form['shipping_zip']) : e($form['billing_zip']),
            'payment_type'       => 'crypto',
            'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
            'crypto_currency'    => $request->crypto_currency ?? '',
            'amount'             => round(session('total.checkout_total') * 0.85, 2),
            'amountInPayCurrency'=> $request->crypto_total,
            'purse'              => $request->purse ?? '',
            'invoiceId'          => $request->invoiceId ?? '',
            'aff'                => session('aff', 0),
            'ref'                => session('referer', ''),
            'refc'               => session('refc', ''),
            'keyword'            => session('keyword', ''),
            'domain_from'        => request()->getHost(),
            'total'              => round(session('total.checkout_total'), 2),
            'shipping'           => session('cart_option.shipping'),
            'products'           => $products_str,
            'saff'               => session('saff', ''),
            'language'           => App::currentLocale(),
            'currency'           => session('currency', 'usd'),
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
            'coupon'             => session('coupon.coupon', ''),
            'bonus'              => '',
            'gift_card_code'     => '', //session('gift_card.gift_card_code', ''),
            'gift_card_discount' => 0, //session('total.coupon_discount', 0),
            'theme'              => 13,
            'coupon_discount'    => session('total.coupon_discount'),
            'sessid'             => $sessid
        ];

        if (checkdnsrr('true-services.net', 'A')) {
            try {
                $response = Http::timeout(10)->post('http://true-services.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа

                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (\Illuminate\Http\Client\RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        }
    }

    public function complete()
    {
        if (empty(session('order'))) {
            return redirect(route('home.index'));
        }

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
                'order_id' => session('order.order_id'),
            ];

            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
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
        ]);
    }

    public function check_payment(Request $request)
    {
        if (!empty(session('crypto'))) {
            $api_key = DB::table('shop_keys')
                           ->where('name_key', '=', 'api_key')
                           ->get('key_data')
                           ->toArray()[0];

            $data = [
                'method'    => 'check_payment',
                'api_key'   => $api_key->key_data,
                'invoiceId' => session('crypto.invoiceId'),
            ];

            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response_payment = Http::timeout(10)->post('http://true-services.net/checkout/order.php', $data);

                    if ($response_payment->successful()) {
                        // Обработка успешного ответа

                        $response_payment = json_decode($response_payment, true);

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

                                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : '';
                            }

                            if (session('cart_option.bonus_id') != 0) {
                                $products[session('cart_option.bonus_id')] = [
                                    'qty'            => 1,
                                    'price'          => session(
                                        'cart_option.bonus_price'
                                    ),
                                    'is_ed_category' => false
                                ];
                            }

                            $products_str = json_encode($products);
                            // $api_key      = DB::table('shop_keys')
                            //                     ->where('name_key', '=', 'api_key')
                            //                     ->get('key_data')
                            //                     ->toArray()[0];

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
                                'crypto_currency'     => e($response_payment['payCurrency']),
                                'invoiceId'           => e($response_payment['invoiceId']),
                                'merchant_id'         => e($response_payment['merchantId']),
                                'purse'               => e($response_payment['purse']),
                                'amount'              => e($response_payment['amount']),
                                'amountInPayCurrency' => e($response_payment['amountInPayCurrency']),
                                'commission'          => e($response_payment['merchantCommission']),
                                'crypto_status'       => e($response_payment['status']),
                                'ip'                  => request()->headers->get('cf-connecting-ip') ? request(
                                )->headers->get('cf-connecting-ip') : request()->ip(),
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
                                'user_agent'          => 'user_agent=' . $request->userAgent() . '&lang=' . request(
                                    )->header(
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
                                'bonus'               => '',
                                'gift_card_code'      => '', //session('gift_card.gift_card_code', ''),
                                'gift_card_discount'  => 0, //session('total.coupon_discount', 0),
                                'theme'               => 13,
                                'coupon_discount'     => session('total.coupon_discount'),
                                'sessid'              => $sessid
                            ];

                            session(['data' => $data]);

                            $email             = e($request->email);
                            $check_order_cache = DB::select("SELECT * FROM order_cache WHERE `message` LIKE '%$email%'");

                            if (count($check_order_cache) == 0) {
                                $data_for_cache             = $data;
                                $data_for_cache['products'] = addslashes($data_for_cache['products']);
                                $order_cache_id             = DB::table('order_cache')->insertGetId([
                                    'message' => json_encode($data_for_cache),
                                    'is_send' => 0
                                ]);
                            } else {
                                $order_cache_id = $check_order_cache[0]->id;
                            }

                            $response = Http::post('http://true-services.net/checkout/order.php', $data);

                            $response = json_decode($response, true);

                            if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && str_contains(
                                        json_encode($response['message']),
                                        'repeat_order'
                                    ))) {
                                DB::delete("DELETE FROM order_cache WHERE `id` = $order_cache_id");
                                session(['order' => $response]);
                                return json_encode(['status' => 'success', 'response' => $response]);
                            } else {
                                return response()->json(json_encode(['status' => 'error', 'text' => 'Service returned an error']));
                            }
                        // }

                        // return response()->json(json_encode($response_payment));


                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response_payment->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
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
                $sessid                        = !empty($product['cart_id']) ? $product['cart_id'] : '';
            }

            if (session('cart_option.bonus_id') != 0) {
                $products[session('cart_option.bonus_id')] = [
                    'qty'            => 1,
                    'price'          => session('cart_option.bonus_price'),
                    'is_ed_category' => false
                ];
            }

            $products_str = json_encode($products);

            $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $billing_state = isset($form['billing_state']) ? e($form['billing_state']) : '';
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
                'coupon'             => session('coupon.coupon', ''),
                'bonus'              => '',
                'gift_card_code'     => '', //session('gift_card.gift_card_code', ''),
                'gift_card_discount' => 0, //session('total.coupon_discount', 0),
                'theme'              => 13,
                'coupon_discount'    => session('total.coupon_discount'),
                'sessid'             => $sessid
            ];

            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
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
            $sessid                        = !empty($product['cart_id']) ? $product['cart_id'] : '';
        }

        if (session('cart_option.bonus_id') != 0) {
            $products[session('cart_option.bonus_id')] = [
                'qty'            => 1,
                'price'          => session('cart_option.bonus_price'),
                'is_ed_category' => false
            ];
        }

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
            'coupon'             => session('coupon.coupon', ''),
            'bonus'              => '',
            'gift_card_code'     => '', //session('gift_card.gift_card_code', ''),
            'gift_card_discount' => 0, //session('total.coupon_discount', 0),
            'theme'              => 13,
            'coupon_discount'    => session('total.coupon_discount'),
            'sessid'             => $sessid
        ];

        session(['data' => $data]);

        $email             = e($form['email']);
        $check_order_cache = DB::select("SELECT * FROM order_cache WHERE `message` LIKE '%$email%'");
        if (count($check_order_cache) == 0) {
            $data_for_cache             = $data;
            $data_for_cache['products'] = addslashes($data_for_cache['products']);
            $order_cache_id             = DB::table('order_cache')->insertGetId([
                'message' => json_encode($data_for_cache),
                'is_send' => 0
            ]);
        } else {
            $order_cache_id = $check_order_cache[0]->id;
        }

        if (checkdnsrr('true-services.net', 'A')) {
            try {
                $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа
                    $response = json_decode($response, true);

                    if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && str_contains(
                                json_encode($response['message']),
                                'repeat_order'
                            ))) {
                        DB::delete("DELETE FROM order_cache WHERE `id` = $order_cache_id");
                        session(['order' => $response]);
                    }

                    return response()->json(['response' => ['status' => 'ok']], 200);
                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (\Illuminate\Http\Client\RequestException $e) {
                // Обработка ошибок запроса, таких как таймаут или недоступность
                Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                $responseData = ['error' => 'Service unavailable'];
            }
        } else {
            session(['order' => 'error']);
            return response()->json(['response' => ['status' => 'ok']], 200);
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
                $sessid                        = !empty($product['cart_id']) ? $product['cart_id'] : '';
            }

            if (session('cart_option.bonus_id') != 0) {
                $products[session('cart_option.bonus_id')] = [
                    'qty'            => 1,
                    'price'          => session('cart_option.bonus_price'),
                    'is_ed_category' => false
                ];
            }

            $products_str = json_encode($products);

            $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $billing_state = isset($form['billing_state']) ? e($form['billing_state']) : '';
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
                'coupon'             => session('coupon.coupon', ''),
                'bonus'              => '',
                'gift_card_code'     => '', //session('gift_card.gift_card_code', ''),
                'gift_card_discount' => 0, //session('total.coupon_discount', 0),
                'theme'              => 13,
                'coupon_discount'    => session('total.coupon_discount'),
                'sessid'             => $sessid
            ];

            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
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
                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : '';
            }

            if (session('cart_option.bonus_id') != 0) {
                $products[session('cart_option.bonus_id')] = [
                    'qty'            => 1,
                    'price'          => session('cart_option.bonus_price'),
                    'is_ed_category' => false
                ];
            }

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
                'coupon'             => session('coupon.coupon', ''),
                'bonus'              => '',
                'gift_card_code'     => '', //session('gift_card.gift_card_code', ''),
                'gift_card_discount' => 0, //session('total.coupon_discount', 0),
                'theme'              => 13,
                'coupon_discount'    => session('total.coupon_discount'),
                'sessid'             => $sessid
            ];

            session(['data' => $data]);

            $email             = e($request->email);
            $check_order_cache = DB::select("SELECT * FROM order_cache WHERE `message` LIKE '%$email%'");
            if (count($check_order_cache) == 0) {
                $data_for_cache             = $data;
                $data_for_cache['products'] = addslashes($data_for_cache['products']);
                $order_cache_id             = DB::table('order_cache')->insertGetId([
                    'message' => json_encode($data_for_cache),
                    'is_send' => 0
                ]);
            } else {
                $order_cache_id = $check_order_cache[0]->id;
            }

            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(10)->post('http://true-services.net/checkout/order.php', $data);

                    if ($response->successful()) {
                        // Обработка успешного ответа

                        $response = json_decode($response, true);

                        if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && str_contains(
                                    json_encode($response['message']),
                                    'repeat_order'
                                ))) {
                            DB::delete("DELETE FROM order_cache WHERE `id` = $order_cache_id");
                            session(['order' => $response]);
                        }

                        return response()->json(['response' => $response], 200);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        }
    }

    public function send_checkout_phone_email(Request $request)
    {
        $sessid      = '';
        $input_type  = $request->input_type;
        $input_value = $request->input_value;

        foreach (session('cart') as $product) {
            $sessid = !empty($product['cart_id']) ? $product['cart_id'] : '';
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

        if (checkdnsrr('true-services.net', 'A')) {
            try {
                $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

                if ($response->successful()) {
                    // Обработка успешного ответа
                    $response = json_decode($response, true);
                    // return response()->json(['response' => ['status' => 'ok']], 200);

                } else {
                    // Обработка ответа с ошибкой (4xx или 5xx)
                    Log::error("Сервис вернул ошибку: " . $response->status());
                    $responseData = ['error' => 'Service returned an error'];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("Ошибка подключения: " . $e->getMessage());
            } catch (\Illuminate\Http\Client\RequestException $e) {
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
                $sessid                        = !empty($product['cart_id']) ? $product['cart_id'] : '';
            }

            if (session('cart_option.bonus_id') != 0) {
                $products[session('cart_option.bonus_id')] = [
                    'qty'            => 1,
                    'price'          => session('cart_option.bonus_price'),
                    'is_ed_category' => false
                ];
            }

            $products_str = json_encode($products);

            $api_key = DB::table('shop_keys')->where('name_key', '=', 'api_key')->get('key_data')->toArray()[0];

            $billing_state = isset($form['billing_state']) ? e($form['billing_state']) : '';
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
                // 'ip'                 => request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip(),
                'ip'                 => '89.187.179.179',
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
                'coupon'             => session('coupon.coupon', ''),
                'bonus'              => '',
                'gift_card_code'     => '', //session('gift_card.gift_card_code', ''),
                'gift_card_discount' => 0, //session('total.coupon_discount', 0),
                'theme'              => 13,
                'coupon_discount'    => session('total.coupon_discount'),
                'sessid'             => $sessid
            ];

            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(10)->post('http://true-services.net/checkout/order_test4.php', $data);

                    if ($response->successful()) {
                        $response = json_decode($response, true);

                        if ($response['status'] == 'ERROR') {
                            return response()->json(['response' => $response], 200);
                        } else {
                            session(['zelle' => [
                                'name' => $response['zelle_name'],
                                'email' => $response['zelle_email'],
                                'orderId' => $response['order_id']
                            ]]);

                            return response()->json(['response' => $response], 200);
                        }
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
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
            session(['order' => [
                'order_id' => session('zelle.orderId')
            ]]);

            session()->forget('zelle');

            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'text' => 'Order ID is empty']);
        }
    }
}