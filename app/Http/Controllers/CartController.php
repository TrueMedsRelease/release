<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CountryInfoCache;
use App\Models\Currency;
use App\Models\Language;
use App\Models\ProductTypeDesc;
use App\Services\ProductServices;
use App\Services\StatisticService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\PhoneCodes;
use Illuminate\Support\Facades\DB;
use Phattarachai\LaravelMobileDetect\Agent;

class CartController extends Controller
{
    public function index()
    {
        if(empty(session('cart')))
        {
            return redirect(route('home.index'));
        }

        StatisticService::SendStatistic('cart');

        $design = session('design') ? session('design') : config('app.design');
        $phone_codes = PhoneCodes::all()->toArray();
        $title = ProductServices::getPageProperties('cart');
        $bestsellers = ProductServices::GetBestsellers($design);
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('cart');
        $first_letters = ProductServices::getFirstLetters();
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if ($domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = HomeController::getAllCountryISO();

        foreach ($codes as $i => $code) {
            $codes[$i] = strtolower($code->iso);
        }

        $web_statistic["params_string"] =
            "aff=" . session('aff', 0) .
            "&saff=" . session('saff', '') .
            "&is_uniq=" . session('uniq', 0) .
            "&keyword=" . session('keyword', '') .
            "&ref=" . session('referer', '') .
            "&domain_from=" . parse_url(config('app.url'), PHP_URL_HOST) .
            "&store_skin=" . str_replace('design_', '', $design) .
            "&page=cart&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . request()->headers->get('cf-connecting-ip') ? request()->headers->get('cf-connecting-ip') : request()->ip();

        return view($design . '.cart', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'Currency' => Currency::class,
            'Language' => Language::class,
            'menu' => $menu,
            'phone_codes' => $phone_codes,
            'title' => $title,
            'page_properties' => $page_properties,
            'cur_category' => '',
            'agent' => $agent,
            'pixel' => $pixel,
            'first_letters' => $first_letters,
            'domain' => $domain,
            'web_statistic' => $web_statistic,
            'codes' => json_encode($codes),
        ]);
    }

    public function cart()
    {
        $design = session('design') ? session('design') : config('app.design');
        $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);
        $products = session('cart');
        $language_id = Language::$languages[App::currentLocale()];

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
        foreach($products as &$item)
        {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['url'] = $desc[$item['product_id']]['url'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;

            if($item['dosage'] != '1card')
            {
                $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                $item['dosage_name'] = $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
            }
            else
            {
                $item['pack_name'] = $item['name'] ;
            }
            $product_total += $item['price'] * $item['q'];

        }

        unset($item);

        $country_info = CountryInfoCache::query()
                        ->where('country_iso2', '=', session('location')['country'])
                        ->get()
                        ->toArray();

        $country_info = $country_info[0];
        $shipping = json_decode($country_info['info'], true);

        if (session('aff') == 1051) {
            $shipping['regular'] = 12.99;
        }

        if(empty(session('cart_option')))
        {
            $shipping_name = $shipping['ems'] != 0 ? 'ems' : 'regular';
            if($shipping_name == 'regular' && $product_total_check >= 200)
            {
                $shipping_price = 0;
            }
            elseif($shipping_name == 'ems' && $product_total_check >= 300)
            {
                $shipping_price = 0;
            }
            else
            {
                $shipping_price = $shipping['ems'] != 0 ? $shipping['ems'] : $shipping['regular'];
            }

            $start_shipping = env('APP_DEFAULT_SHIPPING', 'ems');

            $shipping_name = $start_shipping;
            $shipping_price = $shipping[$start_shipping];

            $ins_start = intval(env('APP_INSUR_ON', 1));
            $secret_start = intval(env('APP_SECRET_ON', 1));

            $option = [
                "shipping" => $shipping_name,
                "shipping_price" => $shipping_price,
                "bonus_id" => 0,
                "bonus_price" => 0,
                "insurance" => $ins_start,
                "insurance_price" => 0,
                "secret_package" => $secret_start,
                "secret_price" => 0,
            ];

            session(['cart_option' => $option]);
        }
        else
        {
            $cart_option = session('cart_option');

            $product_total += $cart_option['bonus_price'];
            $product_total_check += $cart_option['bonus_price'];


            if($cart_option['shipping'] == 'regular' && $product_total_check >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total_check >= 300)
            {
                $cart_option['shipping_price'] = 0;
            }
            else
            {
                $cart_option['shipping_price'] = $shipping[$cart_option['shipping']];
            }

            session(['cart_option' => $cart_option]);
        }

        $bonus = ProductServices::GetBonuses();
        $cards = ProductServices::GetGiftCard();
        $phone_codes = PhoneCodes::all()->toArray();
        $first_letters = ProductServices::getFirstLetters();
        $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if ($domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }
        Cart::update_cart_total();

        $has_card = 0;
        $sum_card = 0;
        $is_only_card = 0;
        $count_card = 0;
        foreach ($products as $product) {
            if ($product['product_id'] == 616) {
                $has_card = 1;
                $sum_card += $product["price"];
                $count_card++;
            }
        }

        if ($has_card && $count_card == count($products) && (int)session('cart_option')['bonus_id'] == 0) {
            $is_only_card = 1;
        }

        $is_only_card_with_bonus = 0;
        if ($count_card == count($products) && $has_card && (int)session('cart_option')['bonus_id'] > 0) {
            $is_only_card_with_bonus = 1;
        }

        if ($is_only_card) {
            foreach ($bonus as $key => $value) {
                if ($value->pack_id == 11630) {
                    unset($bonus[$key]);
                }
            }
        }

        if ($is_only_card_with_bonus) {
            foreach ($bonus as $key => $value) {
                if ($value->pack_id == 11630) {
                    unset($bonus[$key]);
                }
            }
        }


        $returnHTML = view($design . '.ajax.cart_content')->with([
            'design' => $design,
            'products' => $products,
            'product_total' => $product_total,
            'product_total_check' => $product_total_check,
            'shipping' => $shipping,
            'bonus' => $bonus,
            'cards' => $cards,
            'phone_codes' => $phone_codes,
            'Currency' => Currency::class,
            'Language' => Language::class,
            'has_card' => $has_card,
            'is_only_card' => $is_only_card,
            'is_only_card_with_bonus' => $is_only_card_with_bonus,
            'first_letters' => $first_letters,
            'domain' => $domain
        ])->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    public function add($pack_id)
    {
        Cart::add($pack_id);
        return redirect(route('cart.index'));
    }

    public function up(Request $request)
    {
        $design = session('design') ? session('design') : config('app.design');
        $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);
        Cart::add($request->pack_id);
        $products = session('cart');
        $language_id = Language::$languages[App::currentLocale()];

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
        foreach($products as &$item)
        {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['url'] = $desc[$item['product_id']]['url'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            if($item['dosage'] != '1card')
            {
                $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                $item['dosage_name'] = $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
            }
            else
            {
                $item['pack_name'] = $item['name'] ;
            }
            $product_total += $item['price'] * $item['q'];
        }

        unset($item);
        $product_total += session('cart_option')['bonus_price'];
        $product_total_check += session('cart_option')['bonus_price'];

        $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', session('location')['country'])
            ->get()
            ->toArray();

        $country_info = $country_info[0];
        $shipping = json_decode($country_info['info'], true);

        if (session('aff') == 1051) {
            $shipping['regular'] = 12.99;
        }

        $cart_option = session('cart_option');

        if($cart_option['shipping'] == 'regular' && $product_total_check >= 200)
        {
            $cart_option['shipping_price'] = 0;
        }
        elseif($cart_option['shipping'] == 'ems' && $product_total_check >= 300)
        {
            $cart_option['shipping_price'] = 0;
        }
        else
        {
            $cart_option['shipping_price'] = $shipping[$cart_option['shipping']];
        }

        session(['cart_option' => $cart_option]);

        $bonus = ProductServices::GetBonuses();
        $cards = ProductServices::GetGiftCard();
        $phone_codes = PhoneCodes::all()->toArray();
        $first_letters = ProductServices::getFirstLetters();
        $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if ($domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }
        Cart::update_cart_total();

        $has_card = 0;
        $sum_card = 0;
        $is_only_card = 0;
        $count_card = 0;
        foreach ($products as $product) {
            if ($product['product_id'] == 616) {
                $has_card = 1;
                $sum_card += $product["price"];
                $count_card++;
            }
        }

        if ($has_card && $count_card == count($products) && (int)session('cart_option')['bonus_id'] == 0) {
            $is_only_card = 1;
        }

        $is_only_card_with_bonus = 0;
        if ($count_card == count($products) && $has_card && (int)session('cart_option')['bonus_id'] > 0) {
            $is_only_card_with_bonus = 1;
        }

        if ($is_only_card) {
            foreach ($bonus as $key => $value) {
                if ($value->pack_id == 11630) {
                    unset($bonus[$key]);
                }
            }
        }

        if ($is_only_card_with_bonus) {
            foreach ($bonus as $key => $value) {
                if ($value->pack_id == 11630) {
                    unset($bonus[$key]);
                }
            }
        }

        $returnHTML = view($design . '.ajax.cart_content')->with([
            'design' => $design,
            'products' => $products,
            'product_total' => $product_total,
            'product_total_check' => $product_total_check,
            'shipping' => $shipping,
            'cards' => $cards,
            'Currency' => Currency::class,
            'Language' => Language::class,
            'bonus' => $bonus,
            'phone_codes' => $phone_codes,
            'has_card' => $has_card,
            'is_only_card' => $is_only_card,
            'is_only_card_with_bonus' => $is_only_card_with_bonus,
            'first_letters' => $first_letters,
            'domain' => $domain
        ])->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    public function down(Request $request)
    {
        $design = session('design') ? session('design') : config('app.design');
        $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);
        Cart::decrease($request->pack_id);
        $products = session('cart');
        $language_id = Language::$languages[App::currentLocale()];

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
        foreach($products as &$item)
        {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['url'] = $desc[$item['product_id']]['url'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            if($item['dosage'] != '1card')
            {
                $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                $item['dosage_name'] = $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
            }
            else
            {
                $item['pack_name'] = $item['name'] ;
            }
            $product_total += $item['price'] * $item['q'];
        }

        unset($item);
        $product_total += session('cart_option')['bonus_price'];
        $product_total_check += session('cart_option')['bonus_price'];

        $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', session('location')['country'])
            ->get()
            ->toArray();

        $country_info = $country_info[0];
        $shipping = json_decode($country_info['info'], true);

        if (session('aff') == 1051) {
            $shipping['regular'] = 12.99;
        }

        $cart_option = session('cart_option');

        if($cart_option['shipping'] == 'regular' && $product_total_check >= 200)
        {
            $cart_option['shipping_price'] = 0;
        }
        elseif($cart_option['shipping'] == 'ems' && $product_total_check >= 300)
        {
            $cart_option['shipping_price'] = 0;
        }
        else
        {
            $cart_option['shipping_price'] = $shipping[$cart_option['shipping']];
        }

        session(['cart_option' => $cart_option]);

        $bonus = ProductServices::GetBonuses();
        $cards = ProductServices::GetGiftCard();
        $phone_codes = PhoneCodes::all()->toArray();
        $first_letters = ProductServices::getFirstLetters();
        $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if ($domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }
        Cart::update_cart_total();

        $has_card = 0;
        $sum_card = 0;
        $is_only_card = 0;
        $count_card = 0;
        foreach ($products as $product) {
            if ($product['product_id'] == 616) {
                $has_card = 1;
                $sum_card += $product["price"];
                $count_card++;
            }
        }

        if ($has_card && $count_card == count($products) && (int)session('cart_option')['bonus_id'] == 0) {
            $is_only_card = 1;
        }

        $is_only_card_with_bonus = 0;
        if ($count_card == count($products) && $has_card && (int)session('cart_option')['bonus_id'] > 0) {
            $is_only_card_with_bonus = 1;
        }

        if ($is_only_card) {
            foreach ($bonus as $key => $value) {
                if ($value->pack_id == 11630) {
                    unset($bonus[$key]);
                }
            }
        }

        if ($is_only_card_with_bonus) {
            foreach ($bonus as $key => $value) {
                if ($value->pack_id == 11630) {
                    unset($bonus[$key]);
                }
            }
        }

        $returnHTML = view($design . '.ajax.cart_content')->with([
            'design' => $design,
            'products' => $products,
            'product_total' => $product_total,
            'product_total_check' => $product_total_check,
            'shipping' => $shipping,
            'cards' => $cards,
            'Currency' => Currency::class,
            'Language' => Language::class,
            'bonus' => $bonus,
            'phone_codes' => $phone_codes,
            'has_card' => $has_card,
            'is_only_card' => $is_only_card,
            'is_only_card_with_bonus' => $is_only_card_with_bonus,
            'first_letters' => $first_letters,
            'domain' => $domain
        ])->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    public function remove(Request $request)
    {
        Cart::remove($request->pack_id);
        $products = !empty(session('cart')) ? session('cart') : '';
        $language_id = Language::$languages[App::currentLocale()];
        $design = session('design') ? session('design') : config('app.design');

        if($products != '')
        {
            $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);

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
            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['url'] = $desc[$item['product_id']]['url'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                if($item['dosage'] != '1card')
                {
                    $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                    $item['dosage_name'] = $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                }
                else
                {
                    $item['pack_name'] = $item['name'] ;
                }
                $product_total += $item['price'] * $item['q'];
            }



            unset($item);
            $product_total += session('cart_option')['bonus_price'];
            $product_total_check += session('cart_option')['bonus_price'];

            $country_info = CountryInfoCache::query()
                ->where('country_iso2', '=', session('location')['country'])
                ->get()
                ->toArray();

            $country_info = $country_info[0];
            $shipping = json_decode($country_info['info'], true);

            if (session('aff') == 1051) {
                $shipping['regular'] = 12.99;
            }

            $cart_option = session('cart_option');

            if($cart_option['shipping'] == 'regular' && $product_total_check >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total_check >= 300)
            {
                $cart_option['shipping_price'] = 0;
            }
            else
            {
                $cart_option['shipping_price'] = $shipping[$cart_option['shipping']];
            }

            session(['cart_option' => $cart_option]);

            $bonus = ProductServices::GetBonuses();
            $cards = ProductServices::GetGiftCard();
            $phone_codes = PhoneCodes::all()->toArray();
            $first_letters = ProductServices::getFirstLetters();
            $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
            $last_char = strlen($domain) - 1;
            if ($domain[$last_char] == '/') {
                $domain = substr($domain, 0, -1);
            }
            Cart::update_cart_total();

            $has_card = 0;
            $sum_card = 0;
            $is_only_card = 0;
            $count_card = 0;
            foreach ($products as $product) {
                if ($product['product_id'] == 616) {
                    $has_card = 1;
                    $sum_card += $product["price"];
                    $count_card++;
                }
            }

            if ($has_card && $count_card == count($products) && (int)session('cart_option')['bonus_id'] == 0) {
                $is_only_card = 1;
            }

            $is_only_card_with_bonus = 0;
            if ($count_card == count($products) && $has_card && (int)session('cart_option')['bonus_id'] > 0) {
                $is_only_card_with_bonus = 1;
            }

            if ($is_only_card) {
                foreach ($bonus as $key => $value) {
                    if ($value->pack_id == 11630) {
                        unset($bonus[$key]);
                    }
                }
            }

            if ($is_only_card_with_bonus) {
                foreach ($bonus as $key => $value) {
                    if ($value->pack_id == 11630) {
                        unset($bonus[$key]);
                    }
                }
            }

            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'product_total' => $product_total,
                'product_total_check' => $product_total_check,
                'shipping' => $shipping,
                'cards' => $cards,
                'Currency' => Currency::class,
                'Language' => Language::class,
                'bonus' => $bonus,
                'phone_codes' => $phone_codes,
                'has_card' => $has_card,
                'is_only_card' => $is_only_card,
                'is_only_card_with_bonus' => $is_only_card_with_bonus,
                'first_letters' => $first_letters,
                'domain' => $domain
            ])->render();
            return response()->json(array('success' => true, 'html'=>$returnHTML));
        }
        else
        {
            return '';
        }
    }

    public function upgrade(Request $request)
    {
        Cart::upgrade($request->pack_id);
        $products = !empty(session('cart')) ? session('cart') : '';
        $language_id = Language::$languages[App::currentLocale()];
        $design = session('design') ? session('design') : config('app.design');

        if($products != '') //здесь эта проверка поидее не нужна, но пусть будет
        {
            $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);

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
            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['url'] = $desc[$item['product_id']]['url'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                if($item['dosage'] != '1card')
                {
                    $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                    $item['dosage_name'] = $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                }
                else
                {
                    $item['pack_name'] = $item['name'] ;
                }
                $product_total += $item['price'] * $item['q'];
            }

            unset($item);
            $product_total += session('cart_option')['bonus_price'];
            $product_total_check += session('cart_option')['bonus_price'];

            $country_info = CountryInfoCache::query()
                ->where('country_iso2', '=', session('location')['country'])
                ->get()
                ->toArray();

            $country_info = $country_info[0];
            $shipping = json_decode($country_info['info'], true);

            if (session('aff') == 1051) {
                $shipping['regular'] = 12.99;
            }

            $cart_option = session('cart_option');

            if($cart_option['shipping'] == 'regular' && $product_total_check >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total_check >= 300)
            {
                $cart_option['shipping_price'] = 0;
            }
            else
            {
                $cart_option['shipping_price'] = $shipping[$cart_option['shipping']];
            }

            session(['cart_option' => $cart_option]);

            $bonus = ProductServices::GetBonuses();
            $cards = ProductServices::GetGiftCard();
            $phone_codes = PhoneCodes::all()->toArray();
            $first_letters = ProductServices::getFirstLetters();
            $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
            $last_char = strlen($domain) - 1;
            if ($domain[$last_char] == '/') {
                $domain = substr($domain, 0, -1);
            }
            Cart::update_cart_total();

            $has_card = 0;
            $sum_card = 0;
            $is_only_card = 0;
            $count_card = 0;
            foreach ($products as $product) {
                if ($product['product_id'] == 616) {
                    $has_card = 1;
                    $sum_card += $product["price"];
                    $count_card++;
                }
            }

            if ($has_card && $count_card == count($products) && (int)session('cart_option')['bonus_id'] == 0) {
                $is_only_card = 1;
            }

            $is_only_card_with_bonus = 0;
            if ($count_card == count($products) && $has_card && (int)session('cart_option')['bonus_id'] > 0) {
                $is_only_card_with_bonus = 1;
            }

            if ($is_only_card) {
                foreach ($bonus as $key => $value) {
                    if ($value->pack_id == 11630) {
                        unset($bonus[$key]);
                    }
                }
            }

            if ($is_only_card_with_bonus) {
                foreach ($bonus as $key => $value) {
                    if ($value->pack_id == 11630) {
                        unset($bonus[$key]);
                    }
                }
            }

            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'product_total' => $product_total,
                'product_total_check' => $product_total_check,
                'shipping' => $shipping,
                'cards' => $cards,
                'Currency' => Currency::class,
                'Language' => Language::class,
                'bonus' => $bonus,
                'phone_codes' => $phone_codes,
                'has_card' => $has_card,
                'is_only_card' => $is_only_card,
                'is_only_card_with_bonus' => $is_only_card_with_bonus,
                'first_letters' => $first_letters,
                'domain' => $domain
            ])->render();
            return response()->json(array('success' => true, 'html'=>$returnHTML));
        }
        else
        {
            return '';
        }
    }

    public function change_shipping(Request $request)
    {
        $shipping_name = $request->shipping_name;
        $shipping_price = $request->shipping_price;

        $option = session('cart_option');
        $option['shipping'] = $shipping_name;
        $option['shipping_price'] = $shipping_price;

        session(['cart_option' => $option]);
        $design = session('design') ? session('design') : config('app.design');

        $products = !empty(session('cart')) ? session('cart') : '';
        $language_id = Language::$languages[App::currentLocale()];

        if($products != '') //здесь эта проверка поидее не нужна, но пусть будет
        {
            $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);

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
            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['url'] = $desc[$item['product_id']]['url'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                if($item['dosage'] != '1card')
                {
                    $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                    $item['dosage_name'] = $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                }
                else
                {
                    $item['pack_name'] = $item['name'] ;
                }
                $product_total += $item['price'] * $item['q'];
            }

            unset($item);
            $product_total += session('cart_option')['bonus_price'];
            $product_total_check += session('cart_option')['bonus_price'];

            $country_info = CountryInfoCache::query()
                ->where('country_iso2', '=', session('location')['country'])
                ->get()
                ->toArray();

            $country_info = $country_info[0];
            $shipping = json_decode($country_info['info'], true);

            if (session('aff') == 1051) {
                $shipping['regular'] = 12.99;
            }

            $cart_option = session('cart_option');

            if($cart_option['shipping'] == 'regular' && $product_total_check >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total_check >= 300)
            {
                $cart_option['shipping_price'] = 0;
            }
            else
            {
                $cart_option['shipping_price'] = $shipping[$cart_option['shipping']];
            }

            session(['cart_option' => $cart_option]);

            $bonus = ProductServices::GetBonuses();
            $cards = ProductServices::GetGiftCard();
            $phone_codes = PhoneCodes::all()->toArray();
            $first_letters = ProductServices::getFirstLetters();
            $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
            $last_char = strlen($domain) - 1;
            if ($domain[$last_char] == '/') {
                $domain = substr($domain, 0, -1);
            }
            Cart::update_cart_total();

            $has_card = 0;
            $sum_card = 0;
            $is_only_card = 0;
            $count_card = 0;
            foreach ($products as $product) {
                if ($product['product_id'] == 616) {
                    $has_card = 1;
                    $sum_card += $product["price"];
                    $count_card++;
                }
            }

            if ($has_card && $count_card == count($products) && (int)session('cart_option')['bonus_id'] == 0) {
                $is_only_card = 1;
            }

            $is_only_card_with_bonus = 0;
            if ($count_card == count($products) && $has_card && (int)session('cart_option')['bonus_id'] > 0) {
                $is_only_card_with_bonus = 1;
            }

            if ($is_only_card) {
                foreach ($bonus as $key => $value) {
                    if ($value->pack_id == 11630) {
                        unset($bonus[$key]);
                    }
                }
            }

            if ($is_only_card_with_bonus) {
                foreach ($bonus as $key => $value) {
                    if ($value->pack_id == 11630) {
                        unset($bonus[$key]);
                    }
                }
            }

            $design = session('design') ? session('design') : config('app.design');
            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'product_total' => $product_total,
                'product_total_check' => $product_total_check,
                'shipping' => $shipping,
                'cards' => $cards,
                'Currency' => Currency::class,
                'Language' => Language::class,
                'bonus' => $bonus,
                'phone_codes' => $phone_codes,
                'has_card' => $has_card,
                'is_only_card' => $is_only_card,
                'is_only_card_with_bonus' => $is_only_card_with_bonus,
                'first_letters' => $first_letters,
                'domain' => $domain
            ])->render();
            return response()->json(array('success' => true, 'html'=>$returnHTML));
        }
    }

    public function change_bonus(Request $request)
    {
        $bonus_id = $request->bonus_id;
        $bonus_price = $request->bonus_price;
        $design = session('design') ? session('design') : config('app.design');

        if(!empty(session('cart_option')))
        {
            $option = session('cart_option');
            $option['bonus_id'] = $bonus_id;
            $option['bonus_price'] = $bonus_price;

            session(['cart_option' => $option]);
        }

        $products = !empty(session('cart')) ? session('cart') : '';
        $language_id = Language::$languages[App::currentLocale()];

        if($products != '') //здесь эта проверка поидее не нужна, но пусть будет
        {
            $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);

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
            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['url'] = $desc[$item['product_id']]['url'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                if($item['dosage'] != '1card')
                {
                    $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                    $item['dosage_name'] = $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                }
                else
                {
                    $item['pack_name'] = $item['name'] ;
                }
                $product_total += $item['price'] * $item['q'];
            }

            unset($item);
            $product_total += $bonus_price;
            $product_total_check += $bonus_price;

            $country_info = CountryInfoCache::query()
                ->where('country_iso2', '=', session('location')['country'])
                ->get()
                ->toArray();

            $country_info = $country_info[0];
            $shipping = json_decode($country_info['info'], true);

            if (session('aff') == 1051) {
                $shipping['regular'] = 12.99;
            }

            $cart_option = session('cart_option');

            if($cart_option['shipping'] == 'regular' && $product_total_check >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total_check >= 300)
            {
                $cart_option['shipping_price'] = 0;
            }
            else
            {
                $cart_option['shipping_price'] = $shipping[$cart_option['shipping']];
            }

            session(['cart_option' => $cart_option]);

            $bonus = ProductServices::GetBonuses();
            $cards = ProductServices::GetGiftCard();
            $phone_codes = PhoneCodes::all()->toArray();
            $first_letters = ProductServices::getFirstLetters();
            $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
            $last_char = strlen($domain) - 1;
            if ($domain[$last_char] == '/') {
                $domain = substr($domain, 0, -1);
            }
            Cart::update_cart_total();

            $has_card = 0;
            $sum_card = 0;
            $is_only_card = 0;
            $count_card = 0;
            foreach ($products as $product) {
                if ($product['product_id'] == 616) {
                    $has_card = 1;
                    $sum_card += $product["price"];
                    $count_card++;
                }
            }

            if ($has_card && $count_card == count($products) && (int)session('cart_option')['bonus_id'] == 0) {
                $is_only_card = 1;
            }

            $is_only_card_with_bonus = 0;
            if ($count_card == count($products) && $has_card && (int)session('cart_option')['bonus_id'] > 0) {
                $is_only_card_with_bonus = 1;
            }

            if ($is_only_card) {
                foreach ($bonus as $key => $value) {
                    if ($value->pack_id == 11630) {
                        unset($bonus[$key]);
                    }
                }
            }

            if ($is_only_card_with_bonus) {
                foreach ($bonus as $key => $value) {
                    if ($value->pack_id == 11630) {
                        unset($bonus[$key]);
                    }
                }
            }

            $design = session('design') ? session('design') : config('app.design');
            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'product_total' => $product_total,
                'product_total_check' => $product_total_check,
                'shipping' => $shipping,
                'cards' => $cards,
                'Currency' => Currency::class,
                'Language' => Language::class,
                'bonus' => $bonus,
                'phone_codes' => $phone_codes,
                'has_card' => $has_card,
                'is_only_card' => $is_only_card,
                'is_only_card_with_bonus' => $is_only_card_with_bonus,
                'first_letters' => $first_letters,
                'domain' => $domain
            ])->render();
            return response()->json(array('success' => true, 'html'=>$returnHTML));
        }
    }
}
