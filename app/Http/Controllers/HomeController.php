<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use Illuminate\View\View;
use App\Services\ProductServices;
use App\Models\PhoneCodes;
use App\Services\StatisticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Phattarachai\LaravelMobileDetect\Agent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public function index() : View
    {
        StatisticService::SendStatistic('index');
        $design = session('design') ? session('design') : config('app.design');
        $phone_codes = PhoneCodes::all()->toArray();
        $page_properties = ProductServices::getPageProperties('main');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        if (!in_array($design, ['design_7', 'design_8'])) {

            $bestsellers = ProductServices::GetBestsellers($design);
            $menu = ProductServices::GetCategoriesWithProducts($design);
            return view($design . '.index',
            [
                'design' => $design,
                'bestsellers' => $bestsellers,
                'menu' => $menu,
                'phone_codes' => $phone_codes,
                'page_properties' => $page_properties,
                'cur_category' => '',
                'agent' => $agent,
                'Language' => Language::class,
                'Currency' => Currency::class,
                'pixel' => $pixel
            ]);

        } elseif ($design == 'design_7') {
            $product = ProductServices::GetProductInfoByUrl('rybelsus', $design);
            $page_properties->title = 'Rybelsus - ' . str_replace(['http://', 'https://'], '', env('APP_URL'));
            return view($design . '.index',
            [
                'design' => $design,
                'product' => $product,
                'phone_codes' => $phone_codes,
                'page_properties' => $page_properties,
                'cur_category' => '',
                'agent' => $agent,
                'Language' => Language::class,
                'Currency' => Currency::class,
                'pixel' => $pixel
            ]);
        } elseif ($design == 'design_8') {
            $products_urls = ['viagra', 'cialis', 'levitra'];

            foreach ($products_urls as $product_url) {
                $products[$product_url] =  ProductServices::GetProductInfoByUrl($product_url, $design);
            }

            $page_properties->title = 'EdSale - ' . str_replace(['http://', 'https://'], '', env('APP_URL'));
            return view($design . '.index',
            [
                'design' => $design,
                'products' => $products,
                'phone_codes' => $phone_codes,
                'page_properties' => $page_properties,
                'cur_category' => '',
                'agent' => $agent,
                'Language' => Language::class,
                'Currency' => Currency::class,
                'pixel' => $pixel
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
        $page_properties = ProductServices::getPageProperties('first_letter');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view($design . '.first_letter',[
            'design' => $design,
            'products' => $products,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'letter' => $letter,
            'phone_codes' => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category' => '',
            'agent' => $agent,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel
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
        $page_properties = ProductServices::getPageProperties('active');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

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
            'page_properties' => $page_properties,
            'cur_category' => '',
            'agent' => $agent,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel
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
        $agent = new Agent();
        $category = str_replace('-', ' ', $category);

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        session(['category_name' => $category]);

        $page_properties = ProductServices::getPageProperties('category');

        return view($design . '.category',[
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
            'phone_codes' => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category' => $category,
            'agent' => $agent,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel
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
        $page_properties = ProductServices::getPageProperties('disease');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view($design . '.disease',[
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
            'disease' => $disease,
            'phone_codes' => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category' => '',
            'agent' => $agent,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel
        ]);
    }

    public function product($product) : View
    {
        StatisticService::SendStatistic($product);
        $product_name = $product;

        $design = session('design') ? session('design') : config('app.design');
        $language_id = Language::$languages[App::currentLocale()];
        $page_properties = ProductServices::getProductProperties($product);

        $bestsellers = ProductServices::GetBestsellers($design);
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $product = ProductServices::GetProductInfoByUrl($product, $design);
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $product_name = explode('-', $product_name);
        foreach ($product_name as $key => $val) {
            $product_name[$key] = ucfirst($val);
        }
        $product_name = implode(' ', $product_name);

        session(['product_name' => $product_name]);

        return view($design . '.product', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'product' => $product,
            'phone_codes' => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category' => $product['categories'][0]['name'],
            'agent' => $agent,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel
        ]);
    }

    public function product_landing($product, $landing) : View
    {
        if ($landing == 0) {
            return redirect()->route('home.product', $product);
        }

        $design = session('design') ? session('design') : config('app.design');

        if ($design == 7 || $design == 8) {
            return redirect()->route('home.product', $product);
        }

        $product = ProductServices::GetProductInfoByUrl($product, $design);
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        // $product_name = explode('-', $product_name);
        // foreach ($product_name as $key => $val) {
        //     $product_name[$key] = ucfirst($val);
        // }
        // $product_name = implode(' ', $product_name);

        // session(['product_name' => $product_name]);

        // $page_properties = ProductServices::getPageProperties('product');
        // $product_properties_new = $product_properties_new[0];

        // if ($product_properties_new->title != '') {
        //     $page_properties->title = $product_properties_new->title;
        // }

        // if ($product_properties_new->keywords != '') {
        //     $page_properties->keyword = $product_properties_new->keywords;
        // }

        // if ($product_properties_new->description != '') {
        //     $page_properties->description = $product_properties_new->description;
        // }

        return view($design . '.landing', [
            'design' => $design,
            'product' => $product,
            'agent' => $agent,
            'Currency' => Currency::class,
            'pixel' => $pixel
        ]);
    }

    public function about() : View
    {
        StatisticService::SendStatistic('about_us');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('about_us');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view($design . '.about', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'phone_codes' => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category' => '',
            'agent' => $agent,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel
        ]);
    }

    public function help() : View
    {
        StatisticService::SendStatistic('faq');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('faq');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view($design . '.help', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'phone_codes' => $phone_codes,
           'page_properties' => $page_properties,
           'cur_category' => '',
           'agent' => $agent,
           'Language' => Language::class,
           'Currency' => Currency::class,
           'pixel' => $pixel
        ]);
    }

    public function testimonials() : View
    {
        StatisticService::SendStatistic('testimonials');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('testimonials');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view($design . '.testimonials', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'phone_codes' => $phone_codes,
           'page_properties' => $page_properties,
           'cur_category' => '',
           'agent' => $agent,
           'Language' => Language::class,
           'Currency' => Currency::class,
           'pixel' => $pixel
        ]);
    }

    public function delivery() : View
    {
        StatisticService::SendStatistic('shipping');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('shipping');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view($design . '.delivery', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'phone_codes' => $phone_codes,
           'page_properties' => $page_properties,
           'cur_category' => '',
           'agent' => $agent,
           'Language' => Language::class,
           'Currency' => Currency::class,
           'pixel' => $pixel
        ]);
    }

    public function moneyback() : View
    {
        StatisticService::SendStatistic('moneyback');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('moneyback');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view($design . '.moneyback', [
           'design' => $design,
           'bestsellers' => $bestsellers,
           'menu' => $menu,
           'phone_codes' => $phone_codes,
           'page_properties' => $page_properties,
           'cur_category' => '',
           'agent' => $agent,
           'Language' => Language::class,
           'Currency' => Currency::class,
           'pixel' => $pixel
        ]);
    }

    public function contact_us() : View
    {
        StatisticService::SendStatistic('contact_us');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('contact_us');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view($design . '.contact_us', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'phone_codes' => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category' => '',
            'agent' => $agent,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel
        ]);
    }

    public function affiliate() : View
    {
        StatisticService::SendStatistic('affiliate');
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('affiliate');
        $agent = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view($design . '.affiliate', [
            'design' => $design,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'phone_codes' => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category' => '',
            'agent' => $agent,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel
        ]);
    }

    public function login() : View
    {
        StatisticService::SendStatistic('login');
        $design = session('design') ? session('design') : config('app.design');
        $phone_codes = PhoneCodes::all()->toArray();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel = "";
        foreach($pixels as $item)
        {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        return view('login', [
            'design' => $design,
            'phone_codes' => $phone_codes,
            'Language' => Language::class,
            'Currency' => Currency::class,
            'pixel' => $pixel
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
        if (in_array($design, [1,2,3,4,5,6,7,8,9,10])) {
            session(['design' => 'design_' . $design]);
        }
        return redirect()->route('home.index');
    }

    public function set_images($pill) {
        if ($pill) {
            $pill = str_replace('&', '-', (str_replace(' ', '-', strtolower(trim($pill)))));
            $safari = false;

            if (preg_match('/iPhone|iPad|iPod|Macintosh/i', $_SERVER['HTTP_USER_AGENT'])) {
                if (file_exists(public_path() . "/images/" . $pill . ".png") && file_get_contents(public_path() . "/images/" . $pill . ".png") !== "error") {
                    header('Content-type: image/png');
                    echo file_get_contents(public_path() . "/images/" . $pill . ".png");
                    $safari = true;
                } else {
                    $water_string = $_SERVER["HTTP_HOST"];
                    $server_answer = file_get_contents('https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $pill .'&img=png&url=' . $water_string);

                    file_put_contents(public_path() . "/images/" . $pill . ".png", $server_answer);
                    header('Content-type: image/png');
                    $temp = file_get_contents(public_path() . "/images/" . $pill . ".png");
                    if ($temp != "error") {
                        echo $temp;
                        $safari = true;
                    }
                }
                if (!$safari) {
                    if (file_exists(public_path() . "/images/" . $pill . ".jpg") && file_get_contents(public_path() . "/images/" . $pill . ".jpg") !== "error") {
                        header('Content-type: image/png');
                        echo file_get_contents(public_path() . "/images/" . $pill . ".jpg");
                        $safari = true;
                    } else {
                        $water_string = $_SERVER["HTTP_HOST"];
                        $server_answer = file_get_contents('https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $pill .'&img=jpg&url=' . $water_string);

                        file_put_contents(public_path() . "/images/" . $pill . ".jpg", $server_answer);
                        header('Content-type: image/png');
                        $temp = file_get_contents(public_path() . "/images/" . $pill . ".jpg");
                        if ($temp != "error") {
                            echo $temp;
                            $safari = true;
                        }
                    }
                }
                if (!$safari) {
                    if (file_exists(public_path() . "/images/" . $pill . ".jpeg") && file_get_contents(public_path() . "/images/" . $pill . ".jpeg") !== "error") {
                        header('Content-type: image/png');
                        echo file_get_contents(public_path() . "/images/" . $pill . ".jpeg");
                        $safari = true;
                    } else {
                        $water_string = $_SERVER["HTTP_HOST"];
                        $server_answer = file_get_contents('https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $pill .'&img=jpeg&url=' . $water_string);

                        file_put_contents(public_path() . "/images/" . $pill . ".jpeg", $server_answer);
                        header('Content-type: image/png');
                        $temp = file_get_contents(public_path() . "/images/" . $pill . ".jpeg");
                        if ($temp != "error") {
                            echo $temp;
                            $safari = true;
                        }
                    }
                }
            } else {
                if (file_exists(public_path() . "/images/" . $pill . ".webp") && file_get_contents(public_path() . "/images/" . $pill . ".webp") !== "error") {
                    $temp = file_get_contents(public_path() . "/images/" . $pill . ".webp");
                    echo $temp;
                    $safari = true;
                } else {
                    $water_string = $_SERVER["HTTP_HOST"];
                    $server_answer = file_get_contents('https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $pill .'&img=webp&url=' . $water_string);
                    file_put_contents(public_path() . "/images/" . $pill . ".webp", $server_answer);
                    $temp = file_get_contents(public_path() . "/images/" . $pill . ".webp");
                    if ($temp != "error") {
                        echo $temp;
                        $safari = true;
                    }
                }
            }
        } else {
            echo "SERVER ERROR";
        }
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

    public function request_contact_us(Request $request) {

        $name = $request->name;
        $email = $request->email;
        $subject = $request->subject;
        $message = $request->message;
        $captcha = $request->captcha;

        $error = 0;

        if(empty($name)) {
            $error = 1;
        }
        if(empty($email)) {
            $error = 1;
        }
        else {
            if(!preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $email)) {
                $error = 2;
            }
        }
        if(empty($captcha)) {
            $error = 1;
        }
        elseif(!captcha_check($captcha)) {
            $error = 3;
        }

        $data = [
            'page' => 'contact',
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'url_from' => str_replace(['http://', 'https://'], '', env('APP_URL')),
            'aff' => session('aff'),
            'customer_ip' => $request->ip(),
            'customer_user_agent' => $request->userAgent(),
            // 'api_key' => '7c73d5ca242607050422af5a4304ef71',
        ];

        if (!$error) {
            $response = Http::timeout(3)->post('http://true-services.net/support/messages/messages_new.php', $data);
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
            } else if ($error == 3) {
                $response = [
                    'status' => 'error',
                    'text' => __('text.errors_wrong_captcha_value_text')
                ];
            }
        }

        return json_encode($response);
    }

    public function request_affiliate(Request $request) {
        $name = $request->name;
        $email = $request->email;
        $jabber = $request->jabber;
        $message = $request->message;
        $captcha = $request->captcha;

        $error = 0;

        if (empty($name)) {
            $error = 1;
        }

        if (empty($jabber)) {
            $error = 1;
        }

        if (empty($email)) {
            $error = 1;
        } else {
            if (!preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $email)) {
                $error = 2;
            }
        }

        if (empty($captcha)) {
            $error = 1;
        } elseif (!captcha_check($captcha)) {
            $error = 3;
        }

        $data = [
            'page' => 'affiliate',
            'name' => $name,
            'email' => $email,
            'jabber' => $jabber,
            'message' => $message,
            'url_from' => str_replace(['http://', 'https://'], '', env('APP_URL')),
            'aff' => session('aff'),
            'customer_ip' => $request->ip(),
            'customer_user_agent' => $request->userAgent(),
            // 'api_key' => '7c73d5ca242607050422af5a4304ef71',
        ];

        if (!$error) {
            $response = Http::timeout(3)->post('http://true-services.net/support/messages/messages_new.php', $data);
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
            } else if ($error == 3) {
                $response = [
                    'status' => 'error',
                    'text' => __('text.errors_wrong_captcha_value_text')
                ];
            }
        }

        return json_encode($response);
    }

    public function request_login(Request $request) {
        $captcha = $request->captcha;
        $email = $request->email;
        $api_key = 'fe179d54d30c306db93e191751cc7ae9';

        if ($captcha && $email) {
            $data = [
                "email" => $email,
                'method' => 'login',
                'key' => $api_key
            ];

            $response = Http::timeout(3)->post('https://true-services.net/api/customer_api.php', $data);
            $response = json_decode($response, true);

            if ($response['status'] == 'ERROR') {
                if ($response['message'] == 'Unknown customer!') {
                    $result = [
                        'status' => 'error',
                        'text' => __('text.login_email_unknow')
                    ];
                }
            } elseif ($response['status'] == 'OK') {
                if ($response['message'] == 'Access is allowed!') {
                    $_SESSION['user'] = $response['uid'];
                    $lang = App::currentLocale();
                    if ($lang === 'gr')
                        $lang === 'el';
                    if ($lang === 'arb')
                        $lang === 'ar';

                    $result = [
                        'status' => 'success',
                        'url' => 'https://true-help.com/orders.php?eai=' . rawurlencode($response['uid']) . '&lang=' . $lang,
                    ];
                }
            }
        } else {
            $result = [
                'status' => 'error',
                'text' => __('text.errors_empty_field')
            ];
        }

        return json_encode($result);
    }

    public function check_code(Request $request) {
        $captcha = $request->captcha;

        $result = [
            'result' => captcha_check($captcha)
        ];

        return json_encode($result);
    }

    // public static function downloadImageFromWeb() {
    //     $products_images = Product::query()
    //         ->where('is_showed', '=', 1)
    //         ->get(['image'])
    //         ->toArray();

    //     $water_string = str_replace(['http://', 'https://'], '', env('APP_URL'));

    //     foreach ($products_images as $image) {
    //         if (!file_exists(public_path() . '/test_image/' . $image['image'] . '.webp')) {
    //             $server_answer = file_get_contents('https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $image['image'] .'&img=webp&url=' . $water_string);
    //             if ($server_answer != 'error') {
    //                 file_put_contents(public_path() . "/test_image/" . $image['image'] . ".webp", $server_answer);
    //             }
    //         }

    //         if (!file_exists(public_path() . '/test_image/' . $image['image'] . '.png')) {
    //             $server_answer = file_get_contents('https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $image['image'] .'&img=png&url=' . $water_string);
    //             file_put_contents(public_path() . "/images/" . $image['image'] . ".png", $server_answer);
    //             if ($server_answer != 'error') {
    //                 file_put_contents(public_path() . "/test_image/" . $image['image'] . ".webp", $server_answer);
    //             }
    //         }

    //         echo $image['image'];
    //     }
    // }
}
