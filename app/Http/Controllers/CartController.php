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

class CartController extends Controller
{
    public function index()
    {
        if(empty(session('cart')))
        {
            return redirect(route('home.index'));
        }

        StatisticService::SendStatistic('cart');

        $bestsellers = ProductServices::GetBestsellers();
        $menu = ProductServices::GetCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.cart', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'Currency' => Currency::class,
            'Language' => Language::class,
            'menu' => $menu

        ]);
    }

    public function cart()
    {
        $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);
        $products = session('cart');
        $language_id = Language::$languages[App::currentLocale()];

        $types = ProductTypeDesc::query()
        ->where('language_id', '=', $language_id)
        ->get(['type_id', 'name']);

        $product_total = 0;
        foreach($products as &$item)
        {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            if($item['dosage'] != '1card')
            {
                $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
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

        if(empty(session('cart_option')))
        {
            $shipping_name = $shipping['ems'] != 0 ? 'ems' : 'regular';
            if($shipping_name == 'regular' && $product_total >= 200)
            {
                $shipping_price = 0;
            }
            elseif($shipping_name == 'ems' && $product_total >= 300)
            {
                $shipping_price = 0;
            }
            else
            {
                $shipping_price = $shipping['ems'] != 0 ? $shipping['ems'] : $shipping['regular'];
            }
            $option = [
                "shipping" => $shipping_name,
                "shipping_price" => $shipping_price,
                "bonus_id" => 0,
                "bonus_price" => 0,
                "insurance" => true,
                "insurance_price" => 0,
                "secret_package" => true,
                "secret_price" => 0,
            ];

            session(['cart_option' => $option]);
        }
        else
        {
            $cart_option = session('cart_option');

            $product_total += $cart_option['bonus_price'];

            if($cart_option['shipping'] == 'regular' && $product_total >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total >= 300)
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
        Cart::update_cart_total();

        $design = config('app.design');
        $returnHTML = view($design . '.ajax.cart_content')->with([
            'design' => $design,
            'products' => $products,
            'product_total' => $product_total,
            'shipping' => $shipping,
            'bonus' => $bonus,
            'cards' => $cards,
            'Currency' => Currency::class,
            'Language' => Language::class,
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
        $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);
        Cart::add($request->pack_id);
        $products = session('cart');
        $language_id = Language::$languages[App::currentLocale()];

        $types = ProductTypeDesc::query()
        ->where('language_id', '=', $language_id)
        ->get(['type_id', 'name']);

        $product_total = 0;
        foreach($products as &$item)
        {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            if($item['dosage'] != '1card')
            {
                $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
            }
            else
            {
                $item['pack_name'] = $item['name'] ;
            }
            $product_total += $item['price'] * $item['q'];
        }
        unset($item);
        $product_total += session('cart_option')['bonus_price'];

        $country_info = CountryInfoCache::query()
        ->where('country_iso2', '=', session('location')['country'])
        ->get()
        ->toArray();

        $country_info = $country_info[0];
        $shipping = json_decode($country_info['info'], true);

        $cart_option = session('cart_option');

        if($cart_option['shipping'] == 'regular' && $product_total >= 200)
        {
            $cart_option['shipping_price'] = 0;
        }
        elseif($cart_option['shipping'] == 'ems' && $product_total >= 300)
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

        Cart::update_cart_total();

        $design = config('app.design');
        $returnHTML = view($design . '.ajax.cart_content')->with([
            'design' => $design,
            'products' => $products,
            'product_total' => $product_total,
            'shipping' => $shipping,
            'cards' => $cards,
            'Currency' => Currency::class,
            'Language' => Language::class,
            'bonus' => $bonus
        ])->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    public function down(Request $request)
    {
        $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);
        Cart::decrease($request->pack_id);
        $products = session('cart');
        $language_id = Language::$languages[App::currentLocale()];

        $types = ProductTypeDesc::query()
        ->where('language_id', '=', $language_id)
        ->get(['type_id', 'name']);

        $product_total = 0;
        foreach($products as &$item)
        {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            if($item['dosage'] != '1card')
            {
                $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
            }
            else
            {
                $item['pack_name'] = $item['name'] ;
            }
            $product_total += $item['price'] * $item['q'];
        }
        unset($item);
        $product_total += session('cart_option')['bonus_price'];

        $country_info = CountryInfoCache::query()
        ->where('country_iso2', '=', session('location')['country'])
        ->get()
        ->toArray();

        $country_info = $country_info[0];
        $shipping = json_decode($country_info['info'], true);

        $cart_option = session('cart_option');

        if($cart_option['shipping'] == 'regular' && $product_total >= 200)
        {
            $cart_option['shipping_price'] = 0;
        }
        elseif($cart_option['shipping'] == 'ems' && $product_total >= 300)
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

        Cart::update_cart_total();

        $design = config('app.design');
        $returnHTML = view($design . '.ajax.cart_content')->with([
            'design' => $design,
            'products' => $products,
            'product_total' => $product_total,
            'shipping' => $shipping,
            'cards' => $cards,
            'Currency' => Currency::class,
            'Language' => Language::class,
            'bonus' => $bonus
        ])->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    public function remove(Request $request)
    {
        Cart::remove($request->pack_id);
        $products = !empty(session('cart')) ? session('cart') : '';
        $language_id = Language::$languages[App::currentLocale()];

        if($products != '')
        {
            $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);

            $types = ProductTypeDesc::query()
            ->where('language_id', '=', $language_id)
            ->get(['type_id', 'name']);

            $product_total = 0;
            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                if($item['dosage'] != '1card')
                {
                    $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                }
                else
                {
                    $item['pack_name'] = $item['name'] ;
                }
                $product_total += $item['price'] * $item['q'];
            }
            unset($item);
            $product_total += session('cart_option')['bonus_price'];

            $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', session('location')['country'])
            ->get()
            ->toArray();

            $country_info = $country_info[0];
            $shipping = json_decode($country_info['info'], true);

            $cart_option = session('cart_option');

            if($cart_option['shipping'] == 'regular' && $product_total >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total >= 300)
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

            Cart::update_cart_total();

            $design = config('app.design');
            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'product_total' => $product_total,
                'shipping' => $shipping,
                'cards' => $cards,
                'Currency' => Currency::class,
                'Language' => Language::class,
                'bonus' => $bonus
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

        if($products != '') //здесь эта проверка поидее не нужна, но пусть будет
        {
            $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);

            $types = ProductTypeDesc::query()
            ->where('language_id', '=', $language_id)
            ->get(['type_id', 'name']);

            $product_total = 0;
            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                if($item['dosage'] != '1card')
                {
                    $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                }
                else
                {
                    $item['pack_name'] = $item['name'] ;
                }
                $product_total += $item['price'] * $item['q'];
            }
            unset($item);
            $product_total += session('cart_option')['bonus_price'];

            $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', session('location')['country'])
            ->get()
            ->toArray();

            $country_info = $country_info[0];
            $shipping = json_decode($country_info['info'], true);

            $cart_option = session('cart_option');

            if($cart_option['shipping'] == 'regular' && $product_total >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total >= 300)
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

            Cart::update_cart_total();

            $design = config('app.design');
            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'product_total' => $product_total,
                'shipping' => $shipping,
                'cards' => $cards,
                'Currency' => Currency::class,
                'Language' => Language::class,
                'bonus' => $bonus
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

        $products = !empty(session('cart')) ? session('cart') : '';
        $language_id = Language::$languages[App::currentLocale()];

        if($products != '') //здесь эта проверка поидее не нужна, но пусть будет
        {
            $desc = ProductServices::GetProductDesc(Language::$languages[App::currentLocale()]);

            $types = ProductTypeDesc::query()
            ->where('language_id', '=', $language_id)
            ->get(['type_id', 'name']);

            $product_total = 0;
            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                if($item['dosage'] != '1card')
                {
                    $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                }
                else
                {
                    $item['pack_name'] = $item['name'] ;
                }
                $product_total += $item['price'] * $item['q'];
            }
            unset($item);
            $product_total += session('cart_option')['bonus_price'];

            $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', session('location')['country'])
            ->get()
            ->toArray();

            $country_info = $country_info[0];
            $shipping = json_decode($country_info['info'], true);

            $cart_option = session('cart_option');

            if($cart_option['shipping'] == 'regular' && $product_total >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total >= 300)
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

            Cart::update_cart_total();

            $design = config('app.design');
            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'product_total' => $product_total,
                'shipping' => $shipping,
                'cards' => $cards,
                'Currency' => Currency::class,
                'Language' => Language::class,
                'bonus' => $bonus
            ])->render();
            return response()->json(array('success' => true, 'html'=>$returnHTML));
        }
    }

    public function change_bonus(Request $request)
    {
        $bonus_id = $request->bonus_id;
        $bonus_price = $request->bonus_price;

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

            $product_total = 0;
            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                if($item['dosage'] != '1card')
                {
                    $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
                }
                else
                {
                    $item['pack_name'] = $item['name'] ;
                }
                $product_total += $item['price'] * $item['q'];
            }
            unset($item);
            $product_total += $bonus_price;

            $country_info = CountryInfoCache::query()
            ->where('country_iso2', '=', session('location')['country'])
            ->get()
            ->toArray();

            $country_info = $country_info[0];
            $shipping = json_decode($country_info['info'], true);

            $cart_option = session('cart_option');

            if($cart_option['shipping'] == 'regular' && $product_total >= 200)
            {
                $cart_option['shipping_price'] = 0;
            }
            elseif($cart_option['shipping'] == 'ems' && $product_total >= 300)
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

            Cart::update_cart_total();

            $design = config('app.design');
            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'product_total' => $product_total,
                'shipping' => $shipping,
                'cards' => $cards,
                'Currency' => Currency::class,
                'Language' => Language::class,
                'bonus' => $bonus
            ])->render();
            return response()->json(array('success' => true, 'html'=>$returnHTML));
        }
    }
}
