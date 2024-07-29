<?php

namespace App\Http\Controllers;

use App\Services\CacheServices;
use App\Services\GeoIpService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\ProductServices;

class HomeController extends Controller
{
    public function index() : View
    {
        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.index', ['design' => $design, 'bestsellers' => $bestsellers, 'menu' => $menu]);
    }

    public function first_letter($letter) : View
    {
        $products = ProductServices::GetProductByFirstLetter($letter);

        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.first_letter',[
            'design' => $design,
            'products' => $products,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'letter' => $letter,
        ]);
    }

    public function active($active) : View
    {
        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $products = ProductServices::GetProductByActive($active);

        $design = config('app.design');
        return view($design . '.active',[
            'design' => $design,
            'products' => $products,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'active' => $active,
        ]);
    }

    public function category($category) : View
    {

        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $products = ProductServices::GetCategoriesWithProducts($category);

        $design = config('app.design');
        return view($design . '.category',[
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
        ]);
    }

    public function disease($disease) : View
    {

        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $products = ProductServices::GetProductByDisease($disease);

        $design = config('app.design');
        return view($design . '.disease',[
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
            'disease' => $disease
        ]);
    }

    public function product($product) : View
    {
        $design = config('app.design');
        $bestsellers = ProductServices::GetBestsellers();
        $menu = ProductServices::GetCategoriesWithProducts();

        $product = ProductServices::GetProductInfoByUrl($product);
        // $packs = ProductServices::GetPacksById()

        return view($design . '.product', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'product' => $product
        ]);
    }

    public function about() : View
    {
        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.about', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
        ]);
    }

    public function help() : View
    {
        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.help', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
        ]);
    }

    public function testimonials() : View
    {
        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.testimonials', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
        ]);
    }

    public function delivery() : View
    {
        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.delivery', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
        ]);
    }

    public function moneyback() : View
    {
        $bestsellers = ProductServices::GetBestsellers();

        $menu = ProductServices::GetCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.moneyback', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
        ]);
    }

    public function language($locale)
    {
        session(['locale' => $locale]);
        return Redirect::back();
    }

    public function currency($currency)
    {
        session(['currency' => $currency]);
        return Redirect::back();
    }

}
