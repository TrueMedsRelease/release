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
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        if (empty(session('cart'))) {
            return redirect(route('home.index'));
        }

        StatisticService::SendStatistic('checkout');
        StatisticService::SendCheckout();

        $unsent_order = DB::select("SELECT * FROM order_cache WHERE is_send = 0");
        if (count($unsent_order) > 0) {
            foreach ($unsent_order as $order) {
                $response = Http::post('http://true-services.net/checkout/order.php', json_decode($order->message));
                $response = json_decode($response, true);

                if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && $response['message'] === 'repeat_order')) {
                    DB::delete("DELETE FROM order_cache WHERE `id` = {$order->id}");
                }
            }
        }

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'checkout'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $design = session('design') ? session('design') : config('app.design');
        return view('checkout', [
            'pixel' => $pixel,
            'Language' => Language::class,
            'Currency' => Currency::class,
        ]);
    }

    public function checkout()
    {
        $design = session('design') ? session('design') : config('app.design');
        $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);
        $products = session('cart');
        $language_id = Language::$languages[App::currentLocale()];

        CacheServices::CheckCountryInfo();

        $types = ProductTypeDesc::query()
            ->where('language_id', '=', $language_id)
            ->get(['type_id', 'name']);

        $product_total_check = 0;
        foreach($products as $value){
            if($value['product_id'] == 616) {
                continue;
            }
            $product_total_check += $value['price'] * $value['q'];
        }

        $product_total = 0;
        $card_only = true;
        foreach ($products as &$item) {
            $item['name'] = $desc[$item['product_id']]['name'];
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

        $product_total += session('cart_option.bonus_price');
        $product_total_check += session('cart_option.bonus_price');

        $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', session('form.billing_country',session('location.country')))
            ->get()
            ->toArray();

        $country_info = $country_info[0];
        $shipping = json_decode($country_info['info'], true);

        if (session('aff') == 1051) {
            $shipping['regular'] = 12.99;
        }

        $cart_option = session('cart_option');

        $cart_option['insurance_price'] = Cart::ClacInsurance();
        $cart_option['secret_price'] = $shipping['secret_package'];

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
        $countries = CountryInfoCache::all()->toArray();

        Cart::update_cart_total();

        if(!empty(session('form')['phone']) && str_contains(session('form')['phone'], '+'))
        {
            for($i = 0; $i < count($phone_codes); $i++)
            {
                if($phone_codes[$i]['iso'] == session('form')['billing_country'])
                {
                    Session::put('form.phone_code', $phone_codes[$i]['iso']);
                    $code = '+' . $phone_codes[$i]['phonecode'];
                    Session::put('form.phone', str_replace($code, '', session('form')['phone']));
                }
            }
        }

        $states = State::$states;

        $returnHTML = view('checkout_content')->with([
            'Language' => Language::class,
            'Currency' => Currency::class,
            'products' => $products,
            'card_only' => $card_only,
            'bonus' => $bonus,
            'design' => $design,
            'shipping' => $shipping,
            'product_total' => $product_total,
            'product_total_check' => $product_total_check,
            'phone_codes' => $phone_codes,
            'countries' => $countries,
            'states' => $states,

        ])->render();
        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function insurance(Request $request)
    {
        $cart_option = session('cart_option');
        if ($request->val == 0)
            $cart_option['insurance'] = false;
        else
            $cart_option['insurance'] = true;

        session(['cart_option' => $cart_option]);
        session(['form' => $request->all()]);

        Cart::update_cart_total();

        return $this->checkout();
    }

    public function secret_package(Request $request)
    {
        $cart_option = session('cart_option');
        if ($cart_option['secret_package'])
            $cart_option['secret_package'] = false;
        else
            $cart_option['secret_package'] = true;

        session(['cart_option' => $cart_option]);
        session(['form' => $request->all()]);

        return $this->checkout();
    }

    public function change_shipping(Request $request)
    {
        $shipping_name = $request->shipping_name;
        $shipping_price = $request->shipping_price;

        $option = session('cart_option');
        $option['shipping'] = $shipping_name;
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
        $coupon = $request->coupon;
        $data = [
            'method' => 'coupon',
            'api_key' => '7c73d5ca242607050422af5a4304ef71',
            'coupon' => $coupon,
        ];

        $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

        $response = json_decode($response, true);

        if ($response['status'] == 'success') {
            if ($response['coupon']['type'] == 'coupon') {
                $result['coupon'] = $coupon;
                $result['percent'] = $response['coupon']['percent'];
                $result['type'] = $response['coupon']['type'];

                session(['coupon' => $result]);
            }
        } else {
            session()->forget('coupon');
        }

        return $this->checkout();
    }

    public function auth(Request $request)
    {
        $email = $request->email;

        $data = [
            'method' => 'auth',
            'api_key' => '7c73d5ca242607050422af5a4304ef71',
            'email' => $email
        ];

        $response = Http::post('http://true-services.net/checkout/order.php', $data);

        $response = json_decode($response, true);
        $response['email'] = $email;


        if($response['status'] == 'success')
        {
            session(['form' => $response]);
                return $this->checkout();
        }
        else
        {
            Session::put('form.email',$email);
        }

    }

    public function order(Request $request)
    {
        Validator::extend('credit_card_number', function ($attribute, $value, $parameters, $validator) {
            $regex = null;
            $type= 'all';
            $ccNum = str_replace(array('-', ' '), '', $value);
            if (empty($ccNum) || strlen($ccNum) < 13 || strlen($ccNum) > 19) {
                return false;
            }

            $card_number_checksum = '';

            foreach (str_split(strrev((string) $ccNum)) as $i => $d) {
                $card_number_checksum .= $i %2 !== 0 ? $d * 2 : $d;
            }
            // Check if the card number is valid
            if(!(array_sum(str_split($card_number_checksum)) % 10 === 0))
            {
                return false;
            }

            if ($regex !== null) {
                if (is_string($regex) && preg_match($regex, $ccNum)) {
                    return true;
                }
                return false;
            }

            $cards = array(
                'all' => array(
                    'amex'		=> '/^3[4|7]\\d{13}$/',
                    'bankcard'	=> '/^56(10\\d\\d|022[1-5])\\d{10}$/',
                    'diners'	=> '/^(?:3(0[0-5]|[68]\\d)\\d{11})|(?:5[1-5]\\d{14})$/',
                    'disc'		=> '/^(?:6011|650\\d)\\d{12}$/',
                    'electron'	=> '/^(?:417500|4917\\d{2}|4913\\d{2})\\d{10}$/',
                    'enroute'	=> '/^2(?:014|149)\\d{11}$/',
                    'jcb'		=> '/^(3\\d{4}|2100|1800)\\d{11}$/',
                    'maestro'	=> '/^(?:5020|6\\d{3})\\d{12}$/',
                    'mc'		=> '/^5[1-5]\\d{14}$/',
                    'solo'		=> '/^(6334[5-9][0-9]|6767[0-9]{2})\\d{10}(\\d{2,3})?$/',
                    'switch'	=>
                    '/^(?:49(03(0[2-9]|3[5-9])|11(0[1-2]|7[4-9]|8[1-2])|36[0-9]{2})\\d{10}(\\d{2,3})?)|(?:564182\\d{10}(\\d{2,3})?)|(6(3(33[0-4][0-9])|759[0-9]{2})\\d{10}(\\d{2,3})?)$/',
                    'visa'		=> '/^4\\d{12}(\\d{3})?$/',
                    'voyager'	=> '/^8699[0-9]{11}$/'
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
            'phone' => ['required', 'min:5', 'max:16'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone' => ['nullable', 'min:5', 'max:16'],
            'firstname' => ['required', 'max:255'],
            'lastname' => ['required', 'max:255'],
            'billing_country' => ['required', 'max:2'],
            'billing_city' => ['required', 'max:255'],
            'billing_address' => ['required', 'max:255'],
            'billing_zip' => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'card_numb' => ['exclude_unless:payment_type,card', 'required', 'credit_card_number'],
            'bank_name' => ['exclude_unless:payment_type,card', 'required'],
            'expire_date' => ['exclude_unless:payment_type,card', 'required', 'date_format:m/Y' , 'after:now'],
            'cvc_2' => ['exclude_unless:payment_type,card','required', 'min:3', 'max:4']
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        }
        else
        {
            $products = [];
            $sessid = '';

            foreach(session('cart') as $product)
            {
                $products[$product['pack_id']] = ['qty' => $product['q'], 'price' => $product['price'], 'is_ed_category' => false];
                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : '';
            }

            if(session('cart_option.bonus_id') != 0)
            {
                $products[session('cart_option.bonus_id')] = ['qty' => 1, 'price' => session('cart_option.bonus_price'), 'is_ed_category' => false];
            }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;

            $data = [
                'method' => 'order',
                'api_key' => '7c73d5ca242607050422af5a4304ef71',
                'phone' => e('+' . $phone_code . $request->phone),
                'alternative_phone' => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email' => e($request->email),
                'alter_email' => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname' => e($request->firstname),
                'lastname' => e($request->lastname),
                'billing_country' => e($request->billing_country),
                'billing_state' => e($request->billing_state),
                'billing_city' => e($request->billing_city),
                'billing_address' => e($request->billing_address),
                'billing_zip' => e($request->billing_zip),
                'shipping_country' => !empty($request->address_match) ? e($request->shipping_country) : e($request->billing_country),
                'shipping_state' => !empty($request->address_match) ? e($request->shipping_state) : e($request->billing_state),
                'shipping_city' => !empty($request->address_match) ? e($request->shipping_city) : e($request->billing_city),
                'shipping_address' => !empty($request->address_match) ? e($request->shipping_address) : e($request->billing_address),
                'shipping_zip' => !empty($request->address_match) ? e($request->shipping_zip) : e($request->billing_zip),
                'payment_type' => e($request->payment_type),
                'card_holder' => e($request->firstname . ' ' . $request->lastname),
                'card_number' => e($request->card_numb),
                'bank_name' => e($request->bank_name),
                'card_month' => e($request->card_month),
                'card_year' => e($request->card_year),
                'card_cvv' => e($request->cvc_2),
                'ip' => request()->ip(),
                'aff' => session('aff', 0),
                'ref' => session('referer', ''),
                'refc' => session('refc', ''),
                'keyword' => session('keyword', ''),
                'domain_from' => request()->getHost(),
                'total' => session('total.checkout_total'),
                'shipping' => session('cart_option.shipping'),
                'products' => $products_str,
                'saff' => session('saff', ''),
                'language' => App::currentLocale(),
                'currency' => session('currency'),
                'user_agent' => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header('Accept-Language') . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint' => '',
                'product_total' => session('total.product_total'),
                'customer_id' => '',
                'reorder' => 0,
                'reorder_discount' => 0,
                'shipping_price' => session('total.shipping_total'),
                'insurance' => session('total.insurance'),
                'secret_package' => session('total.secret_package'),
                'store_skin' => config('app.design'),
                'recurring_period' => 0,
                'coupon' => session('coupon.coupon', ''),
                'bonus' => '',
                'gift_card_code' => '',
                'gift_card_discount' => 0,
                'theme' => 13,
                'coupon_discount' => session('total.coupon_discount'),
                'sessid' => $sessid
            ];

            session(['data' => $data]);

            $check_order_cache = DB::select("SELECT * FROM order_cache WHERE `message` LIKE '%{e($request->email)}%'");
            if(count($check_order_cache) == 0)
            {
                $order_cache_id = DB::table('order_cache')->insertGetId([
                    'message' => json_encode($data),
                    'is_send' => 0
                ]);
            }
            else
            {
                $order_cache_id = $check_order_cache[0]['id'];
            }


            $response = Http::post('http://true-services.net/checkout/order.php', $data);

            $response = json_decode($response, true);

            if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && $response['message'] === 'repeat_order')) {
                DB::delete("DELETE FROM order_cache WHERE `id` = $order_cache_id");
                session(['order' => $response]);
            }

            // session(['order' => $response]);

            return response()->json(['response' => $response], 200);
        }
    }

    public function paypal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'min:5', 'max:16'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone' => ['nullable', 'min:5', 'max:16'],
            'firstname' => ['required', 'max:255'],
            'lastname' => ['required', 'max:255'],
            'billing_country' => ['required', 'max:2'],
            'billing_city' => ['required', 'max:255'],
            'billing_address' => ['required', 'max:255'],
            'billing_zip' => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip' => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        }
        else
        {
            $products = [];
            $sessid = '';

            foreach(session('cart') as $product)
            {
                $products[$product['pack_id']] = ['qty' => $product['q'], 'price' => $product['price'], 'is_ed_category' => false];
                $sessid = !empty($product['cart_id']) ? $product['cart_id'] : '';
            }

            if(session('cart_option.bonus_id') != 0)
            {
                $products[session('cart_option.bonus_id')] = ['qty' => 1, 'price' => session('cart_option.bonus_price'), 'is_ed_category' => false];
            }

            $products_str = json_encode($products);

            // $products = str_replace(['[',']'], '', $products);

            $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
            $phone_code = $phone_code->phonecode;

            $data = [
                'method' => 'order',
                'api_key' => '7c73d5ca242607050422af5a4304ef71',
                'phone' => e('+' . $phone_code . $request->phone),
                'alternative_phone' => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                'email' => e($request->email),
                'alter_email' => !empty($request->alt_email) ? e($request->alt_email) : '',
                'firstname' => e($request->firstname),
                'lastname' => e($request->lastname),
                'billing_country' => e($request->billing_country),
                'billing_state' => e($request->billing_state),
                'billing_city' => e($request->billing_city),
                'billing_address' => e($request->billing_address),
                'billing_zip' => e($request->billing_zip),
                'shipping_country' => !empty($request->address_match) ? e($request->shipping_country) : e($request->billing_country),
                'shipping_state' => !empty($request->address_match) ? e($request->shipping_state) : e($request->billing_state),
                'shipping_city' => !empty($request->address_match) ? e($request->shipping_city) : e($request->billing_city),
                'shipping_address' => !empty($request->address_match) ? e($request->shipping_address) : e($request->billing_address),
                'shipping_zip' => !empty($request->address_match) ? e($request->shipping_zip) : e($request->billing_zip),
                'payment_type' => e('paypal'),
                'ip' => request()->ip(),
                'aff' => session('aff', 0),
                'ref' => session('referer', ''),
                'refc' => session('refc', ''),
                'keyword' => session('keyword', ''),
                'domain_from' => request()->getHost(),
                'total' => session('total.checkout_total'),
                'shipping' => session('cart_option.shipping'),
                'products' => $products_str,
                'saff' => session('saff', ''),
                'language' => App::currentLocale(),
                'currency' => session('currency'),
                'user_agent' => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header('Accept-Language') . '&screen_resolution=' . $request->screen_resolution . '&customer_date=' . $request->customer_date,
                'fingerprint' => '',
                'product_total' => session('total.product_total'),
                'customer_id' => '',
                'reorder' => 0,
                'reorder_discount' => 0,
                'shipping_price' => session('total.shipping_total'),
                'insurance' => session('total.insurance'),
                'secret_package' => session('total.secret_package'),
                'store_skin' => config('app.design'),
                'recurring_period' => 0,
                'coupon' => session('coupon.coupon', ''),
                'bonus' => '',
                'gift_card_code' => '',
                'gift_card_discount' => 0,
                'theme' => 13,
                'coupon_discount' => session('total.coupon_discount'),
                'sessid' => $sessid
            ];

            session(['data' => $data]);

            $check_order_cache = DB::select("SELECT * FROM order_cache WHERE `message` LIKE '%{e($request->email)}%'");
            if(count($check_order_cache) == 0)
            {
                $order_cache_id = DB::table('order_cache')->insertGetId([
                    'message' => json_encode($data),
                    'is_send' => 0
                ]);
            }
            else
            {
                $order_cache_id = $check_order_cache[0]['id'];
            }

            $response = Http::post('http://true-services.net/checkout/order.php', $data);

            $response = json_decode($response, true);

            if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && $response['message'] === 'repeat_order')) {
                DB::delete("DELETE FROM order_cache WHERE `id` = $order_cache_id");
                session(['order' => $response]);
            }

            return response()->json(['response' => $response], 200);
        }

    }

    public function crypto_info(Request $request)
    {
        $data = [
            'method' => 'get_crypt',
            'api_key' => '7c73d5ca242607050422af5a4304ef71',
            'price' => session('total.checkout_total') * 0.85,
            'email' => $request->email,
            'currency' => $request->currency,
         ];

        $response = Http::post('http://true-services.net/checkout/order.php', $data);

        $response = json_decode($response, true);
        $response['crypto_total'] = Currency::Convert(session('total.checkout_total') * 0.85);
        $response['qr'] = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $response['purse'];
        $response['currency'] = $request->currency;

        session(['crypto' => $response]);

        return response()->json(json_encode($response));
    }

    public function validate_for_crypt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'min:5', 'max:16'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'alt_email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'alt_phone' => ['nullable', 'min:5', 'max:16'],
            'firstname' => ['required', 'max:255'],
            'lastname' => ['required', 'max:255'],
            'billing_country' => ['required', 'max:2'],
            'billing_city' => ['required', 'max:255'],
            'billing_address' => ['required', 'max:255'],
            'billing_zip' => ['required', 'max:255'],
            'shipping_country' => !empty($request->address_match) ? ['required', 'max:2'] : [],
            'shipping_city' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_address' => !empty($request->address_match) ? ['required', 'max:255'] : [],
            'shipping_zip' => !empty($request->address_match) ? ['required', 'max:255'] : [],
        ]);

        session(['form' => $request->all()]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->messages()->toArray() as $key => $error) {
                $errors[] = ['message' => $error[0], 'field' => $key];
            }
            return response()->json(['errors' => $errors], 422);
        }
        else
        {
            session(['form.payment_type' => 'crypto']);

            $form = json_encode(session('form'));

            $data = [
                'method' => 'save_order_data',
                'api_key' => '7c73d5ca242607050422af5a4304ef71',
                'form' => $form,
             ];

            $response = Http::post('http://true-services.net/checkout/order.php', $data);
        }
    }

    public function complete()
    {
        if (empty(session('order'))) {
            return redirect(route('home.index'));
        }

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'complete'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        if(isset($_GET['id']))
        {
            $id = $_GET['id'];
            $data = [
                'method' => 'hold',
                'id' => $id,
                'order_id' => session('order.order_id'),
             ];

            try{
                $response = Http::timeout(1)->post('http://true-services.net/checkout/order.php', $data);
            }
            catch(\Exception $e)
            {
                Log::error('Ошибка запроса: ' . $e->getMessage());
            }
        }

        $design = session('design') ? session('design') : config('app.design');
        return view('complete')->with([
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel,
        ]);
    }

    public function check_payment(Request $request)
    {
        if(!empty(session('crypto')))
        {
            $data = [
                'method' => 'check_payment',
                'api_key' => '7c73d5ca242607050422af5a4304ef71',
                'invoiceId' => session('crypto.invoiceId'),
             ];

            $response_payment = Http::post('http://true-services.net/checkout/order.php', $data);

            $response_payment = json_decode($response_payment, true);

            session(['check_payment' => $response_payment]);

            if($response_payment['status'] == 3 || $response_payment == 5)
            {
                $phone_code = PhoneCodes::where('iso', '=', $request->billing_country)->first();
                $phone_code = $phone_code->phonecode;

                $products = [];
                $sessid = '';

                foreach(session('cart') as $product)
                {
                    $products[$product['pack_id']] = ['qty' => $product['q'], 'price' => $product['price'], 'is_ed_category' => false];
                    $sessid = !empty($product['cart_id']) ? $product['cart_id'] : '';
                }

                if(session('cart_option.bonus_id') != 0)
                {
                    $products[session('cart_option.bonus_id')] = ['qty' => 1, 'price' => session('cart_option.bonus_price'), 'is_ed_category' => false];
                }

                $products_str = json_encode($products);

                $data = [
                    'method' => 'order',
                    'api_key' => '7c73d5ca242607050422af5a4304ef71',
                    'phone' => e('+' . $phone_code . $request->phone),
                    'alternative_phone' => !empty($request->alt_phone) ? e('+' . $phone_code . $request->alt_phone) : '',
                    'email' => e($request->email),
                    'alter_email' => !empty($request->alt_email) ? e($request->alt_email) : '',
                    'firstname' => e($request->firstname),
                    'lastname' => e($request->lastname),
                    'billing_country' => e($request->billing_country),
                    'billing_state' => e($request->billing_state),
                    'billing_city' => e($request->billing_city),
                    'billing_address' => e($request->billing_address),
                    'billing_zip' => e($request->billing_zip),
                    'shipping_country' => !empty($request->address_match) ? e($request->shipping_country) : e($request->billing_country),
                    'shipping_state' => !empty($request->address_match) ? e($request->shipping_state) : e($request->billing_state),
                    'shipping_city' => !empty($request->address_match) ? e($request->shipping_city) : e($request->billing_city),
                    'shipping_address' => !empty($request->address_match) ? e($request->shipping_address) : e($request->billing_address),
                    'shipping_zip' => !empty($request->address_match) ? e($request->shipping_zip) : e($request->billing_zip),
                    'payment_type' => e($request->payment_type),
                    'crypto_currency' => e($response_payment['payCurrency']),
                    'invoiceId' => e($response_payment['invoiceId']),
                    'merchant_id' => e($response_payment['merchantId']),
                    'purse' => e($response_payment['purse']),
                    'amount' => e($response_payment['amount']),
                    'amountInPayCurrency' => e($response_payment['amountInPayCurrency']),
                    'commission' => e($response_payment['merchantCommission']),
                    'crypto_status' => e($response_payment['status']),
                    'ip' => request()->ip(),
                    'aff' => session('aff', 0),
                    'ref' => session('referer', ''),
                    'refc' => session('refc', ''),
                    'keyword' => session('keyword', ''),
                    'domain_from' => request()->getHost(),
                    'total' => session('total.checkout_total'),
                    'shipping' => session('cart_option.shipping'),
                    'products' => $products_str,
                    'saff' => session('saff', ''),
                    'language' => App::currentLocale(),
                    'currency' => session('currency'),
                    'user_agent' => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header('Accept-Language') . '&screen_resolution=' . $request->screen_resolution,
                    'fingerprint' => '',
                    'product_total' => session('total.product_total'),
                    'customer_id' => '',
                    'reorder' => 0,
                    'reorder_discount' => 0,
                    'shipping_price' => session('total.shipping_total'),
                    'insurance' => session('total.insurance'),
                    'secret_package' => session('total.secret_package'),
                    'store_skin' => config('app.design'),
                    'recurring_period' => 0,
                    'coupon' => session('coupon.coupon', ''),
                    'bonus' => '',
                    'gift_card_code' => '',
                    'gift_card_discount' => 0,
                    'theme' => 13,
                    'coupon_discount' => session('total.coupon_discount'),
                    'sessid' => $sessid
                ];

                session(['data' => $data]);

                $check_order_cache = DB::select("SELECT * FROM order_cache WHERE `message` LIKE '%{e($request->email)}%'");
                if(count($check_order_cache) == 0)
                {
                    $order_cache_id = DB::table('order_cache')->insertGetId([
                        'message' => json_encode($data),
                        'is_send' => 0
                    ]);
                }
                else
                {
                    $order_cache_id = $check_order_cache[0]['id'];
                }

                $response = Http::post('http://true-services.net/checkout/order.php', $data);

                $response = json_decode($response, true);

                if ($response['status'] === 'SUCCESS' || (($response['status'] === 'ERROR' || $response['status'] === 'error') && $response['message'] === 'repeat_order')) {
                    DB::delete("DELETE FROM order_cache WHERE `id` = $order_cache_id");
                    session(['order' => $response]);
                }
            }

            return response()->json(json_encode($response_payment));
        }
    }
}
