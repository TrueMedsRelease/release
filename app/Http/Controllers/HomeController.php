<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\Product;
use App\Services\CurrencyServices;
use App\Services\LanguageServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\ProductServices;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Illuminate;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function index() : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();

        if (!in_array($design, ['design_7', 'design_8'])) {

            $bestsellers = ProductServices::GetBestsellers($design);
            $menu = ProductServices::GetCategoriesWithProducts($design);
            return view($design . '.index', ['design' => $design, 'bestsellers' => $bestsellers, 'menu' => $menu, 'languages' => $languages, 'currencies' => $currencies]);

        } elseif ($design == 'design_7') {
            $product = ProductServices::GetProductInfoByUrl('rybelsus', $design);
            return view($design . '.index', ['design' => $design, 'product' => $product, 'languages' => $languages, 'currencies' => $currencies]);
        } else {
            $products_urls = ['viagra', 'cialis', 'levitra'];

            foreach ($products_urls as $product_url) {
                $products[$product_url] =  ProductServices::GetProductInfoByUrl($product_url, $design);
            }
            return view($design . '.index', ['design' => $design, 'products' => $products, 'languages' => $languages, 'currencies' => $currencies]);
        }
    }

    public function first_letter($letter) : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $products = ProductServices::GetProductByFirstLetter($letter, $design);

        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);


        return view($design . '.first_letter',[
            'design' => $design,
            'products' => $products,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'letter' => $letter,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    public function active($active) : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        $products = ProductServices::GetProductByActive($active, $design);

        return view($design . '.active',[
            'design' => $design,
            'products' => $products,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'active' => $active,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    public function category($category) : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        $products = ProductServices::GetCategoriesWithProducts($design, $category);

        return view($design . '.category',[
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    public function disease($disease) : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        $products = ProductServices::GetProductByDisease($disease, $design);

        return view($design . '.disease',[
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
            'disease' => $disease,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    public function product($product) : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);
        $menu = ProductServices::GetCategoriesWithProducts($design);

        $product = ProductServices::GetProductInfoByUrl($product, $design);
        // $packs = ProductServices::GetPacksById()

        return view($design . '.product', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'product' => $product,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    public function about() : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.about', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    public function help() : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.help', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'languages' => $languages,
           'currencies' => $currencies
        ]);
    }

    public function testimonials() : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.testimonials', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'languages' => $languages,
           'currencies' => $currencies
        ]);
    }

    public function delivery() : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.delivery', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'languages' => $languages,
           'currencies' => $currencies
        ]);
    }

    public function moneyback() : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.moneyback', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'languages' => $languages,
           'currencies' => $currencies
        ]);
    }

    public function contact_us() : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.contact_us', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    public function affiliate() : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.affiliate', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    public function language($locale)
    {
        session(['locale' => $locale]);
        return Redirect::back();
    }

    public function currency($currency)
    {
        $coef = Currency::GetCoef($currency);
        session(['currency' => $currency]);
        session(['currency_c' => $coef]);
        return Redirect::back();
    }

}
