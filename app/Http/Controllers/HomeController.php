<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\ProductServices;

class HomeController extends Controller
{
    public function index() : View
    {
        $product_service = new ProductServices();

        $bestsellers = $product_service->GetBestsellers();

        $menu = $product_service->GetAllCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.index', ['design' => $design, 'bestsellers' => $bestsellers, 'menu' => $menu]);
    }

    public function first_letter($letter) : View
    {
        $product_service = new ProductServices();

        $products = $product_service->GetProductByFirstLetter($letter);

        $bestsellers = $product_service->GetBestsellers();

        $menu = $product_service->GetAllCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.first_letter',[
            'design' => $design,
            'products' => $products,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'letter' => $letter,
        ]);
    }

    public function about() : View
    {
        $product_service = new ProductServices();

        $bestsellers = $product_service->GetBestsellers();

        $menu = $product_service->GetAllCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.about', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
        ]);
    }

    public function help() : View
    {
        $product_service = new ProductServices();

        $bestsellers = $product_service->GetBestsellers();

        $menu = $product_service->GetAllCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.help', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
        ]);
    }

    public function testimonials() : View
    {
        $product_service = new ProductServices();

        $bestsellers = $product_service->GetBestsellers();

        $menu = $product_service->GetAllCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.testimonials', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
        ]);
    }

    public function delivery() : View
    {
        $product_service = new ProductServices();

        $bestsellers = $product_service->GetBestsellers();

        $menu = $product_service->GetAllCategoriesWithProducts();

        $design = config('app.design');
        return view($design . '.delivery', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
        ]);
    }

    public function moneyback() : View
    {
        $product_service = new ProductServices();

        $bestsellers = $product_service->GetBestsellers();

        $menu = $product_service->GetAllCategoriesWithProducts();

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

}
