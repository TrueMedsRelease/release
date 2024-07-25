<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Language;
use App\Models\ProductTypeDesc;
use App\Services\ProductServices;
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

        $bestsellers = ProductServices::GetBestsellers();
        $menu = ProductServices::GetCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.cart', [
            'design' => $design,
            'bestsellers' => $bestsellers,
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

        foreach($products as &$item)
        {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
        }
        unset($item);

        $cart_total = 0;
        if (!empty(session('cart'))) {
            foreach (session('cart') as $value) {
                $cart_total += $value['price'] * $value['q'];
            }
        }

        $design = config('app.design');
        $returnHTML = view($design . '.ajax.cart_content')->with([
            'design' => $design,
            'products' => $products,
            'cart_total' => $cart_total,
        ])->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));

        // return view($design . '.ajax.cart_content', [
        //     'design' => $design,
        //     'products' => $products
        // ]);
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

        foreach($products as &$item)
        {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
        }
        unset($item);

        $cart_total = 0;
        if (!empty(session('cart'))) {
            foreach (session('cart') as $value) {
                $cart_total += $value['price'] * $value['q'];
            }
        }

        $design = config('app.design');
        $returnHTML = view($design . '.ajax.cart_content')->with([
            'design' => $design,
            'products' => $products,
            'cart_total' => $cart_total
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

        foreach($products as &$item)
        {
            $item['name'] = $desc[$item['product_id']]['name'];
            $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
            $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
        }
        unset($item);

        $cart_total = 0;
        if (!empty(session('cart'))) {
            foreach (session('cart') as $value) {
                $cart_total += $value['price'] * $value['q'];
            }
        }

        $design = config('app.design');
        $returnHTML = view($design . '.ajax.cart_content')->with([
            'design' => $design,
            'products' => $products,
            'cart_total' => $cart_total
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

            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
            }
            unset($item);

            $cart_total = 0;
            if (!empty(session('cart'))) {
                foreach (session('cart') as $value) {
                    $cart_total += $value['price'] * $value['q'];
                }
            }

            $design = config('app.design');
            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'cart_total' => $cart_total
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

            foreach($products as &$item)
            {
                $item['name'] = $desc[$item['product_id']]['name'];
                $item['type_name'] = $types->where('type_id', '=', $item['type'])->first()->name;
                $item['pack_name'] = $item['name'] . ' ' . $item['dosage'] . ' x ' . $item['num'] . ' ' . $item['type_name'];
            }
            unset($item);

            $cart_total = 0;
            if (!empty(session('cart'))) {
                foreach (session('cart') as $value) {
                    $cart_total += $value['price'] * $value['q'];
                }
            }

            $design = config('app.design');
            $returnHTML = view($design . '.ajax.cart_content')->with([
                'design' => $design,
                'products' => $products,
                'cart_total' => $cart_total
            ])->render();
            return response()->json(array('success' => true, 'html'=>$returnHTML));
        }
        else
        {
            return '';
        }
    }
}
