<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use App\Services\CurrencyServices;
use App\Services\LanguageServices;
use Illuminate\Support\Facades\Redirect;
use App\Services\CacheServices;
use App\Services\GeoIpService;
use Illuminate\View\View;
use App\Services\ProductServices;
use App\Models\PhoneCodes;
use App\Services\StatisticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index() : View
    {
        StatisticService::SendStatistic('index');
        $design = session('design') ? session('design') : config('app.design');
        $phone_codes = PhoneCodes::all()->toArray();

        if (!in_array($design, ['design_7', 'design_8'])) {

            $bestsellers = ProductServices::GetBestsellers($design);
            $menu = ProductServices::GetCategoriesWithProducts($design);
            return view($design . '.index',
            [
                'design' => $design,
                'bestsellers' => $bestsellers,
                'menu' => $menu,
                'phone_codes' => $phone_codes,
                'Language' => Language::class,
                'Currency' => Currency::class
            ]);

        } elseif ($design == 'design_7') {
            $product = ProductServices::GetProductInfoByUrl('rybelsus', $design);
            return view($design . '.index',
            [
                'design' => $design,
                'product' => $product,
                'phone_codes' => $phone_codes,
                'Language' => Language::class,
                'Currency' => Currency::class
            ]);
        } else {
            $products_urls = ['viagra', 'cialis', 'levitra'];

            foreach ($products_urls as $product_url) {
                $products[$product_url] =  ProductServices::GetProductInfoByUrl($product_url, $design);
            }
            return view($design . '.index',
            [
                'design' => $design,
                'products' => $products,
                'phone_codes' => $phone_codes,
                'Language' => Language::class,
                'Currency' => Currency::class
            ]);
        }
    }

    public function first_letter($letter) : View
    {
        StatisticService::SendStatistic('first_letter');
        $design = session('design') ? session('design') : config('app.design');
        $products = ProductServices::GetProductByFirstLetter($letter, $design);
        $phone_codes = PhoneCodes::all()->toArray();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu = ProductServices::GetCategoriesWithProducts($design);


        return view($design . '.first_letter',[
            'design' => $design,
            'products' => $products,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'letter' => $letter,
            'phone_codes' => $phone_codes,
            'Language' => Language::class,
            'Currency' => Currency::class
        ]);
    }

    public function active($active)
    {
        StatisticService::SendStatistic('active');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        $products = ProductServices::GetProductByActive($active, $design);


        if (count($products) == 1) {
            return redirect(route('home.product', $products[0]['url']));
        }

        return view($design . '.active',[
            'design' => $design,
            'products' => $products,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'active' => $active,
            'phone_codes' => $phone_codes,
            'Language' => Language::class,
            'Currency' => Currency::class
        ]);
    }

    public function category($category) : View
    {
        StatisticService::SendStatistic('category');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        $products = ProductServices::GetCategoriesWithProducts($design, $category);

        return view($design . '.category',[
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
            'phone_codes' => $phone_codes,
            'Language' => Language::class,
            'Currency' => Currency::class
        ]);
    }

    public function disease($disease) : View
    {
        StatisticService::SendStatistic('disease');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        $products = ProductServices::GetProductByDisease($disease, $design);

        return view($design . '.disease',[
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
            'disease' => $disease,
            'phone_codes' => $phone_codes,
            'Language' => Language::class,
            'Currency' => Currency::class
        ]);
    }

    public function product($product) : View
    {
        StatisticService::SendStatistic($product);
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $product = ProductServices::GetProductInfoByUrl($product, $design);

        return view($design . '.product', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'product' => $product,
            'phone_codes' => $phone_codes,
            'Language' => Language::class,
            'Currency' => Currency::class
        ]);
    }

    public function about() : View
    {
        StatisticService::SendStatistic('about_us');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.about', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'phone_codes' => $phone_codes,
            'Language' => Language::class,
            'Currency' => Currency::class
        ]);
    }

    public function help() : View
    {
        StatisticService::SendStatistic('faq');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.help', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'phone_codes' => $phone_codes,
           'Language' => Language::class,
           'Currency' => Currency::class
        ]);
    }

    public function testimonials() : View
    {
        StatisticService::SendStatistic('testimonials');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.testimonials', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'phone_codes' => $phone_codes,
           'Language' => Language::class,
           'Currency' => Currency::class
        ]);
    }

    public function delivery() : View
    {
        StatisticService::SendStatistic('shipping');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.delivery', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'phone_codes' => $phone_codes,
           'Language' => Language::class,
           'Currency' => Currency::class
        ]);
    }

    public function moneyback() : View
    {
        StatisticService::SendStatistic('moneyback');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.moneyback', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'phone_codes' => $phone_codes,
           'Language' => Language::class,
           'Currency' => Currency::class
        ]);
    }

    public function contact_us() : View
    {
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.contact_us', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'phone_codes' => $phone_codes,
            'Language' => Language::class,
            'Currency' => Currency::class
        ]);
    }

    public function affiliate() : View
    {
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);

        return view($design . '.affiliate', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'phone_codes' => $phone_codes,
            'Language' => Language::class,
            'Currency' => Currency::class
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

    public function design($design)
    {
        session(['design' => 'design_' . $design]);
        return Redirect::back();
    }

    public function request_call(Request $request)
    {
        $phone = $request->phone;

        $error = 0;
        if (empty($phone)) {
            $error = 1;
        }

        $data = [
            'method' => 'send_request',
            // 'api_key' => '7c73d5ca242607050422af5a4304ef71',
            'phone' => $phone,
            'shop' => str_replace(['http://', 'https://'], '', env('APP_URL')),
            'aff' => session('aff'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ];

        if (!$error) {
            $response = Http::timeout(3)->post('http://true-services.net/support/messages/phone_request.php', $data);
            $response = json_decode($response, true);
        } else {
            $response = [
                'status' => 'error',
                'text' => __('text.errors_empty_field')
            ];
        }

        return json_encode($response);
    }

    public function request_subscribe(Request $request)
    {
        $email = $request->email;

        $error = 0;
        if (empty($email)) {
            $error = 1;
        }

        if(!preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $email)) {
            $error = 2;
        }

        $data = [
            'method' => 'subscribe',
            // 'api_key' => '7c73d5ca242607050422af5a4304ef71',
            'email' => $email,
            'shop' => str_replace(['http://', 'https://'], '', env('APP_URL')),
            'aff' => session('aff'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ];

        if (!$error) {
            $response = Http::timeout(3)->post('http://true-services.net/support/messages/subscribe.php', $data);
            $response = json_decode($response, true);
        } else {
            if ($error == 1) {
                $response = [
                    'status' => 'error',
                    'text' => __('text.errors_empty_field')
                ];
            } else if ($error == 2) {
                $response = [
                    'status' => 'error',
                    'text' => __('text.errors_wrong_email')
                ];
            }
        }

        return json_encode($response);
    }
}
