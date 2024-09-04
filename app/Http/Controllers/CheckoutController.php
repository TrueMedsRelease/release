<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CountryInfoCache;
use App\Models\Currency;
use App\Models\Language;
use App\Models\PhoneCodes;
use App\Models\ProductTypeDesc;
use App\Models\State;
use App\Services\ProductServices;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function index()
    {
        if (empty(session('cart'))) {
            return redirect(route('home.index'));
        }
        $design = session('design') ? session('design') : config('app.design');
        return view($design . '.checkout');
    }

    public function checkout()
    {
        $design = session('design') ? session('design') : config('app.design');
        $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()], $design);
        $products = session('cart');
        $language_id = Language::$languages[App::currentLocale()];

        $types = ProductTypeDesc::query()
            ->where('language_id', '=', $language_id)
            ->get(['type_id', 'name']);

        $product_total = 0;
        $card_only = true;
        foreach ($products as &$item) {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            if ($item['dosage'] != '1card') {
                $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                $card_only = false;
            } else {
                $item['pack_name'] = $item['name'];
            }
            $product_total += $item['price'] * $item['q'];
        }
        unset($item);

        $product_total += session('cart_option.bonus_price');

        $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', session('form.billing_country',session('location.country')))
            ->get()
            ->toArray();

        $country_info = $country_info[0];
        $shipping = json_decode($country_info['info'], true);


        $cart_option = session('cart_option');

        $cart_option['insurance_price'] = Cart::ClacInsurance();
        $cart_option['secret_price'] = $shipping['secret_package'];

        if ($cart_option['shipping'] == 'regular' && $product_total >= 200) {
            $cart_option['shipping_price'] = 0;
        } elseif ($cart_option['shipping'] == 'ems' && $product_total >= 300) {
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

        $returnHTML = view($design . '.ajax.checkout_content')->with([
            'Language' => Language::class,
            'Currency' => Currency::class,
            'products' => $products,
            'card_only' => $card_only,
            'bonus' => $bonus,
            'design' => $design,
            'shipping' => $shipping,
            'product_total' => $product_total,
            'phone_codes' => $phone_codes,
            'countries' => $countries,
            'states' => $states,
        ])->render();
        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function insurance()
    {
        $cart_option = session('cart_option');
        if ($cart_option['insurance'])
            $cart_option['insurance'] = false;
        else
            $cart_option['insurance'] = true;

        session(['cart_option' => $cart_option]);

        Cart::update_cart_total();

        return $this->checkout();
    }

    public function secret_package()
    {
        $cart_option = session('cart_option');
        if ($cart_option['secret_package'])
            $cart_option['secret_package'] = false;
        else
            $cart_option['secret_package'] = true;

        session(['cart_option' => $cart_option]);

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

            foreach(session('cart') as $product)
            {
                $products[$product['pack_id']] = ['qty' => $product['q'], 'price' => $product['price'], 'is_ed_category' => false];
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
                'aff' => 0, // need aff
                'ref' => '',
                'refc' => '',
                'keyword' => '0',
                'domain_from' => '',
                'total' => session('total.all'),
                'shipping' => session('cart_option.shipping'),
                'products' => $products_str,
                'saff' => '',
                'language' => App::currentLocale(),
                'currency' => session('currency'),
                'user_agent' => 'user_agent=' . $request->userAgent() . '&lang=' . request()->header('Accept-Language'),
                'fingerprint' => '',
                'product_total' => session('total.product_total'),
                'customer_id' => '',
                'reorder' => 0,
                'reorder_discount' => 0,
                'shipping_price' => session('total.shipping_total'),
                'insurance' => session('session.insurance'),
                'secret_package' => session('session.secret_package'),
                'store_skin' => config('app.design'),
                'recurring_period' => 0,
                'coupon' => session('coupon.coupon', ''),
                'bonus' => '',
                'gift_card_code' => '',
                'gift_card_discount' => 0,
                'theme' => 13,
                'coupon_discount' => session('total.coupon_discount'),
                'sessid' => ''
            ];

            session(['data' => $data]);

            $response = Http::post('http://true-services.net/checkout/order_test.php', $data);

            $response = json_decode($response, true);

            session(['order' => $response]);

            return response()->json(['response' => $response], 200);
        }
    }

    public function complete() : View
    {
        if (empty(session('order'))) {
            return redirect(route('home.index'));
        }

        $design = session('design') ? session('design') : config('app.design');
        return view($design . '.complete')->with([
            'Language' => Language::class,
            'Currency' => Currency::class,
        ]);
    }
}
