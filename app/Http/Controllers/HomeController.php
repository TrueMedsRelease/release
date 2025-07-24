<?php

namespace App\Http\Controllers;

use App\Helpers\RequestHelper;
use App\Models\Currency;
use App\Models\Language;
use App\Models\PhoneCodes;
use App\Models\ProductDesc;
use App\Services\ProductServices;
use App\Services\StatisticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Phattarachai\LaravelMobileDetect\Agent;

class HomeController extends Controller
{
    public function index()
    {
        $statisticPromise = StatisticService::SendStatistic('index');

        $design          = session('design') ? session('design') : config('app.design');
        $phone_codes     = PhoneCodes::all()->toArray();
        $page_properties = ProductServices::getPageProperties('main');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $codes = $this->getAllCountryISO();
        $language_id = isset(Language::$languages[App::currentLocale()]) ? Language::$languages[App::currentLocale()] : Language::$languages['en'];

        foreach ($codes as $i => $code) {
            $codes[$i] = strtolower($code->iso);
        }

        $device = ProductServices::getDevice($agent);

        $web_statistic["params_string"] =
            "aff=" . session('aff', 0) .
            "&saff=" . session('saff', '') .
            "&is_uniq=" . session('uniq', 0) .
            "&keyword=" . session('keyword', '') .
            "&ref=" . session('referer', '') .
            "&domain_from=" . parse_url(config('app.url'), PHP_URL_HOST) .
            "&store_skin=" . str_replace('design_', '', $design) .
            "&page=main&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();


        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        if (!in_array($design, ['design_7', 'design_8'])) {
            $bestsellers = ProductServices::GetBestsellers($design);
            $menu        = ProductServices::GetCategoriesWithProducts($design);

            return view(
                $design . '.index',
                [
                    'design'          => $design,
                    'bestsellers'     => $bestsellers,
                    'menu'            => $menu,
                    'phone_codes'     => $phone_codes,
                    'page_properties' => $page_properties,
                    'cur_category'    => '',
                    'agent'           => $agent,
                    'Language'        => Language::class,
                    'Currency'        => Currency::class,
                    'pixel'           => $pixel,
                    'first_letters'   => $first_letters,
                    'domain'          => $domain,
                    'web_statistic'   => $web_statistic,
                    'codes'           => json_encode($codes),
                ]
            );
        } elseif ($design == 'design_7') {
            $product_url = DB::table('product_desc')
                ->where('product_id', '=', 511)
                ->where('language_id', '=', $language_id)
                ->get(['url'])
                ->toArray();

            if (isset($product_url[0])) {
                $product = ProductServices::GetProductInfoByUrl($product_url[0]->url);
            }

            return view(
                $design . '.index',
                [
                    'design'          => $design,
                    'product'         => $product,
                    'phone_codes'     => $phone_codes,
                    'page_properties' => $page_properties,
                    'cur_category'    => '',
                    'agent'           => $agent,
                    'Language'        => Language::class,
                    'Currency'        => Currency::class,
                    'pixel'           => $pixel,
                    'first_letters'   => $first_letters,
                    'domain'          => $domain,
                    'web_statistic'   => $web_statistic,
                    'codes'           => json_encode($codes),
                ]
            );
        } elseif ($design == 'design_8') {
            // $products_urls = ['viagra', 'cialis', 'levitra'];
            $product_ids = [285, 233, 255];

            $products_urls = DB::table('product_desc')
                ->whereIn('product_id', $product_ids)
                ->where('language_id', $language_id)
                ->orderByRaw("FIELD(product_id, " . implode(',', $product_ids) . ")")
                ->get(['url'])
                ->toArray();

            if (!empty($products_urls)) {
                foreach ($products_urls as $product_url) {
                    $products[$product_url->url] = ProductServices::GetProductInfoByUrl($product_url->url, $design);
                }
            }

            // $page_properties->title = 'EdSale - ' . $domain;
            return view(
                $design . '.index',
                [
                    'design'          => $design,
                    'products'        => $products,
                    'phone_codes'     => $phone_codes,
                    'page_properties' => $page_properties,
                    'cur_category'    => '',
                    'agent'           => $agent,
                    'Language'        => Language::class,
                    'Currency'        => Currency::class,
                    'pixel'           => $pixel,
                    'first_letters'   => $first_letters,
                    'domain'          => $domain,
                    'web_statistic'   => $web_statistic,
                    'codes'           => json_encode($codes),
                ]
            );
        }
    }

    public function first_letter($char): View
    {
        $statisticPromise = StatisticService::SendStatistic('first_letter');

        $design      = session('design') ? session('design') : config('app.design');
        $products    = ProductServices::GetProductByFirstLetter($char, $design);
        $phone_codes = PhoneCodes::all()->toArray();
        $bestsellers = ProductServices::GetBestsellers($design);

        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('first_letter');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=first_letter&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.first_letter', [
            'design'          => $design,
            'products'        => $products,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'letter'          => $char,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function active($active)
    {
        $statisticPromise = StatisticService::SendStatistic('active');

        if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
            $active = str_replace(
                [__('text.text_aff_domain_1', [], 'en') . '_', '_' . __('text.text_aff_domain_2', [], 'en')],
                '',
                $active
            );
        } else {
            $active = str_replace([__('text.text_aff_domain_1') . '_', '_' . __('text.text_aff_domain_2')],
                '',
                $active);
        }

        $design      = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu        = ProductServices::GetCategoriesWithProducts($design);

        $products        = ProductServices::GetProductByActive($active, $design);
        $page_properties = ProductServices::getPageProperties('active');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        if (empty($products)) {
            return redirect(route('home.index'));
        }

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        if (count($products) == 1) {
            return redirect(route('home.product', $products[0]['url']));
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=active&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        return view($design . '.active', [
            'design'          => $design,
            'products'        => $products,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'active'          => $active,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function category($category): View
    {
        if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
            $category = str_replace(
                [__('text.text_aff_domain_1', [], 'en') . '_', '_' . __('text.text_aff_domain_2', [], 'en')],
                '',
                $category
            );
        } else {
            $category = str_replace([__('text.text_aff_domain_1') . '_', '_' . __('text.text_aff_domain_2')],
                '',
                $category);
        }

        $statisticPromise = StatisticService::SendStatistic('category');

        $design      = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu        = ProductServices::GetCategoriesWithProducts($design);

        $products      = ProductServices::GetCategoriesWithProducts($design, $category);
        $first_letters = ProductServices::getFirstLetters();
        $agent         = new Agent();
        $category      = str_replace('-', ' ', $category);

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        session(['category_name' => $category]);

        $page_properties = ProductServices::getPageProperties('category');

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=category&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.category', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'products'        => $products,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => $category,
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function disease($disease)
    {
        if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
            $disease = str_replace(
                [__('text.text_aff_domain_1', [], 'en') . '_', '_' . __('text.text_aff_domain_2', [], 'en')],
                '',
                $disease
            );
        } else {
            $disease = str_replace([__('text.text_aff_domain_1') . '_', '_' . __('text.text_aff_domain_2')],
                '',
                $disease);
        }

        $statisticPromise = StatisticService::SendStatistic('disease');

        $design      = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $menu        = ProductServices::GetCategoriesWithProducts($design);

        $products        = ProductServices::GetProductByDisease($disease, $design);
        $page_properties = ProductServices::getPageProperties('disease');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        if (empty($products)) {
            return redirect(route('home.index'));
        }

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=disease&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.disease', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'products'        => $products,
            'disease'         => $disease,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function product($product)
    {
        $language_id = isset(Language::$languages[App::currentLocale()]) ? Language::$languages[App::currentLocale()] : Language::$languages['en'];

        if (is_numeric($product)) {
            $product_id = $product;

            $product_name_begin = DB::table('product_desc')
                ->where('product_id', '=', $product_id)
                ->where('language_id', '=', $language_id)
                ->get(['url'])
                ->toArray();

            if (empty($product_name_begin) || !isset($product_name_begin[0])) {
                return redirect()->route('home.index');
            }

            $product = $product_name_begin[0]->url;
        }

        // if ($product == 'a-ret-gel') {
        //     $product = 'a-ret gel';
        // }

        if (request('landing', 0) == 1) {
            return $this->product_landing($product, 1);
        }

        $statisticPromise = StatisticService::SendStatistic($product);

        $product_name = $product;

        if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
            $product = str_replace(
                [__('text.text_aff_domain_1', [], 'en') . '_', '_' . __('text.text_aff_domain_2', [], 'en')],
                '',
                $product
            );
        } else {
            $product = str_replace([__('text.text_aff_domain_1') . '_', '_' . __('text.text_aff_domain_2')],
                '',
                $product);
        }

        $design          = session('design') ? session('design') : config('app.design');
        $page_properties = ProductServices::getProductProperties($product);

        $bestsellers = ProductServices::GetBestsellers($design);
        $menu        = ProductServices::GetCategoriesWithProducts($design);
        $phone_codes = PhoneCodes::all()->toArray();
        $product     = ProductServices::GetProductInfoByUrl($product, $design);

        if (empty($product['packs'])) {
            return redirect()->route('home.index');
        }

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        if (!$product) {
            return redirect()->route('home.index');
        }

        $first_letters = ProductServices::getFirstLetters();
        $agent         = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $product_name = explode('-', $product_name);
        foreach ($product_name as $key => $val) {
            $product_name[$key] = ucfirst($val);
        }
        $product_name = implode(' ', $product_name);

        session(['product_name' => $product_name]);

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=product&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        $recommendation = ProductServices::getProductRecommendation($product['id']);

        return view($design . '.product', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'product'         => $product,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => $product['categories'][0]['name'],
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
            'recommendation'  => $recommendation,
        ]);
    }

    public function product_landing($product, $landing)
    {
        if ($landing == 0) {
            return redirect()->route('home.product', $product);
        }

        $design = session('design') ? session('design') : config('app.design');

        if ($design == 7 || $design == 8) {
            return redirect()->route('home.product', $product);
        }

        if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
            $product = str_replace(
                [__('text.text_aff_domain_1', [], 'en') . '_', '_' . __('text.text_aff_domain_2', [], 'en')],
                '',
                $product
            );
        } else {
            $product = str_replace([__('text.text_aff_domain_1') . '_', '_' . __('text.text_aff_domain_2')],
                '',
                $product);
        }

        $product = ProductServices::GetProductInfoByUrl($product, $design);
        $agent   = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
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

        return response()
            ->view($design . '.landing', [
                'design'   => $design,
                'product'  => $product,
                'agent'    => $agent,
                'Currency' => Currency::class,
                'pixel'    => $pixel,
                'host'     => $_SERVER['SERVER_NAME']
            ])
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Content-Type', 'application/javascript');
    }

    public function about(): View
    {
        $statisticPromise = StatisticService::SendStatistic('about_us');

        $design          = session('design') ? session('design') : config('app.design');
        $bestsellers     = ProductServices::GetBestsellers($design);
        $phone_codes     = PhoneCodes::all()->toArray();
        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('about_us');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=about_us&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.about', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function help(): View
    {
        $statisticPromise = StatisticService::SendStatistic('faq');

        $design          = session('design') ? session('design') : config('app.design');
        $bestsellers     = ProductServices::GetBestsellers($design);
        $phone_codes     = PhoneCodes::all()->toArray();
        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('faq');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=faq&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.help', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function testimonials(): View
    {
        $statisticPromise = StatisticService::SendStatistic('testimonials');

        $design          = session('design') ? session('design') : config('app.design');
        $bestsellers     = ProductServices::GetBestsellers($design);
        $phone_codes     = PhoneCodes::all()->toArray();
        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('testimonials');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=testimonials&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.testimonials', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function delivery(): View
    {
        $statisticPromise = StatisticService::SendStatistic('shipping');

        $design          = session('design') ? session('design') : config('app.design');
        $bestsellers     = ProductServices::GetBestsellers($design);
        $phone_codes     = PhoneCodes::all()->toArray();
        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('shipping');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=shipping&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.delivery', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function moneyback(): View
    {
        $statisticPromise = StatisticService::SendStatistic('moneyback');

        $design          = session('design') ? session('design') : config('app.design');
        $bestsellers     = ProductServices::GetBestsellers($design);
        $phone_codes     = PhoneCodes::all()->toArray();
        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('moneyback');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=moneyback&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.moneyback', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function contact_us($default_subject = 0): View
    {
        $statisticPromise = StatisticService::SendStatistic('contact_us');

        $design          = session('design') ? session('design') : config('app.design');
        $bestsellers     = ProductServices::GetBestsellers($design);
        $phone_codes     = PhoneCodes::all()->toArray();
        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('contact_us');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

        foreach ($codes as $i => $code) {
            $codes[$i] = strtolower($code->iso);
        }

        $subjects = [
            0  => __('text.contact_us_subject_0'),
            1  => __('text.contact_us_subject_1'),
            2  => __('text.contact_us_subject_2'),
            3  => __('text.contact_us_subject_3'),
            4  => __('text.contact_us_subject_4'),
            5  => __('text.contact_us_subject_5'),
            6  => __('text.contact_us_subject_6'),
            7  => __('text.contact_us_subject_7'),
            8  => __('text.contact_us_subject_8'),
            9  => __('text.contact_us_subject_9'),
            10 => __('text.contact_us_subject_10'),
            11 => __('text.contact_us_subject_11'),
        ];

        $default_subject = 0;

        if (str_contains(request()->server('HTTP_REFERER'), 'search')) {
            $default_subject = 7;
        }

        $web_statistic["params_string"] =
            "aff=" . session('aff', 0) .
            "&saff=" . session('saff', '') .
            "&is_uniq=" . session('uniq', 0) .
            "&keyword=" . session('keyword', '') .
            "&ref=" . session('referer', '') .
            "&domain_from=" . parse_url(config('app.url'), PHP_URL_HOST) .
            "&store_skin=" . str_replace('design_', '', $design) .
            "&page=contact_us&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.contact_us', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
            'subjects'        => $subjects,
            'default_subject' => $default_subject,
            'error_subject'   => __('text.contact_us_subject_0'),
        ]);
    }

    public function affiliate(): View
    {
        $statisticPromise = StatisticService::SendStatistic('affiliate');

        $design          = session('design') ? session('design') : config('app.design');
        $bestsellers     = ProductServices::GetBestsellers($design);
        $phone_codes     = PhoneCodes::all()->toArray();
        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('affiliate');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=affiliate&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.affiliate', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function login(): View
    {
        $statisticPromise = StatisticService::SendStatistic('login');

        $design      = session('design') ? session('design') : config('app.design');
        $phone_codes = PhoneCodes::all()->toArray();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view('login', [
            'design'      => $design,
            'phone_codes' => $phone_codes,
            'Language'    => Language::class,
            'Currency'    => Currency::class,
            'pixel'       => $pixel
        ]);
    }

    public function language($locale)
    {
        $lang = Language::GetLanguageByCountry($locale);
        App::setLocale($lang);
        session(['locale' => $lang]);

        if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
            $back_url = Redirect::back()->getTargetUrl();

            if (in_array($locale, ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                $new_text_1 = __('text.text_aff_domain_1', [], 'en');
                $new_text_2 = __('text.text_aff_domain_2', [], 'en');
            } else {
                $new_text_1 = __('text.text_aff_domain_1', [], $locale);
                $new_text_2 = __('text.text_aff_domain_2', [], $locale);
            }

            if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                $back_url = str_replace(__('text.text_aff_domain_1', [], 'en'), $new_text_1, $back_url);
                $back_url = str_replace(__('text.text_aff_domain_2', [], 'en'), $new_text_2, $back_url);
            } else {
                $back_url = str_replace(__('text.text_aff_domain_1'), $new_text_1, $back_url);
                $back_url = str_replace(__('text.text_aff_domain_2'), $new_text_2, $back_url);
            }

            return Redirect::to($back_url);
        } else {
            return Redirect::back();
        }
    }

    public function language_with_url($url, $locale)
    {
        session(['locale' => $locale]);

        if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
            if ($url) {
                $back_url = $url;
            } else {
                $back_url = Redirect::back()->getTargetUrl();
            }

            if (in_array($locale, ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                $new_text_1 = __('text.text_aff_domain_1', [], 'en');
                $new_text_2 = __('text.text_aff_domain_2', [], 'en');
            } else {
                $new_text_1 = __('text.text_aff_domain_1', [], $locale);
                $new_text_2 = __('text.text_aff_domain_2', [], $locale);
            }

            if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                $back_url = str_replace(__('text.text_aff_domain_1', [], 'en'), $new_text_1, $back_url);
                $back_url = str_replace(__('text.text_aff_domain_2', [], 'en'), $new_text_2, $back_url);
            } else {
                $back_url = str_replace(__('text.text_aff_domain_1'), $new_text_1, $back_url);
                $back_url = str_replace(__('text.text_aff_domain_2'), $new_text_2, $back_url);
            }

            return Redirect::to($back_url);
        } else {
            if ($url) {
                return redirect('/' . $url);
            } else {
                return Redirect::back();
            }
        }
    }

    public function currency($currency)
    {
        $coef = Currency::GetCoef($currency);
        session(['currency' => $currency]);
        session(['currency_c' => $coef]);
        return Redirect::back();
    }

    public function currency_with_url($url, $currency)
    {
        $coef = Currency::GetCoef($currency);
        session(['currency' => $currency]);
        session(['currency_c' => $coef]);

        if ($url) {
            return redirect('/' . $url);
        } else {
            return Redirect::back();
        }
    }

    public function design($design)
    {
        if (in_array($design, [1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13])) {
            session(['design' => 'design_' . $design]);
        }

        return redirect()->route('home.index');
    }

    public function design_with_url($url, $design)
    {
        if (in_array($design, [1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13])) {
            session(['design' => 'design_' . $design]);
        }

        if ($url) {
            return redirect('/' . $url);
        } else {
            return redirect()->route('home.index');
        }
    }

    public function set_images($pill)
    {
        if ($pill) {
            $pill = str_replace('&', '-', (str_replace(' ', '-', strtolower(trim($pill)))));

            if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]
                )) {
                $parts = explode('_', $pill, 2);
                $pill  = $parts[1];
            }

            $safari = false;

            if (str_contains($_SERVER['HTTP_USER_AGENT'], 'iPhone') || str_contains(
                    $_SERVER['HTTP_USER_AGENT'],
                    'iPad'
                ) || str_contains($_SERVER['HTTP_USER_AGENT'], 'iPod') || str_contains(
                    $_SERVER['HTTP_USER_AGENT'],
                    'Macintosh'
                )) {
                if (file_exists(public_path() . "/images/" . $pill . ".png") && file_get_contents(
                                                                                    public_path(
                                                                                    ) . "/images/" . $pill . ".png"
                                                                                ) !== "error") {
                    $safari = true;
                    return response(file_get_contents(public_path() . "/images/" . $pill . ".png"))->header(
                        'Content-type',
                        'image/png'
                    );
                } else {
                    $water_string  = $_SERVER["HTTP_HOST"];
                    $server_answer = file_get_contents(
                        'https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $pill . '&img=png&url=' . $water_string
                    );

                    file_put_contents(public_path() . "/images/" . $pill . ".png", $server_answer);
                    $temp = file_get_contents(public_path() . "/images/" . $pill . ".png");
                    if ($temp != "error") {
                        $safari = true;
                        return response($temp)->header('Content-type', 'image/png');
                    }
                }
                if (!$safari) {
                    if (file_exists(public_path() . "/images/" . $pill . ".jpg") && file_get_contents(
                                                                                        public_path(
                                                                                        ) . "/images/" . $pill . ".jpg"
                                                                                    ) !== "error") {
                        $safari = true;
                        return response(file_get_contents(public_path() . "/images/" . $pill . ".jpg"))->header(
                            'Content-type',
                            'image/png'
                        );
                    } else {
                        $water_string  = $_SERVER["HTTP_HOST"];
                        $server_answer = file_get_contents(
                            'https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $pill . '&img=jpg&url=' . $water_string
                        );

                        file_put_contents(public_path() . "/images/" . $pill . ".jpg", $server_answer);
                        $temp = file_get_contents(public_path() . "/images/" . $pill . ".jpg");
                        if ($temp != "error") {
                            $safari = true;
                            return response($temp)->header('Content-type', 'image/png');
                        }
                    }
                }
                if (!$safari) {
                    if (file_exists(public_path() . "/images/" . $pill . ".jpeg") && file_get_contents(
                                                                                         public_path(
                                                                                         ) . "/images/" . $pill . ".jpeg"
                                                                                     ) !== "error") {
                        $safari = true;
                        return response(file_get_contents(public_path() . "/images/" . $pill . ".jpeg"))->header(
                            'Content-type',
                            'image/png'
                        );
                    } else {
                        $water_string  = $_SERVER["HTTP_HOST"];
                        $server_answer = file_get_contents(
                            'https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $pill . '&img=jpeg&url=' . $water_string
                        );

                        file_put_contents(public_path() . "/images/" . $pill . ".jpeg", $server_answer);
                        $temp = file_get_contents(public_path() . "/images/" . $pill . ".jpeg");
                        if ($temp != "error") {
                            $safari = true;
                            return response($temp)->header('Content-type', 'image/png');
                        }
                    }
                }
            } else {
                if (file_exists(public_path() . "/images/" . $pill . ".webp") && file_get_contents(
                                                                                     public_path(
                                                                                     ) . "/images/" . $pill . ".webp"
                                                                                 ) !== "error") {
                    return response(file_get_contents(public_path() . "/images/" . $pill . ".webp"))->header(
                        'Content-type',
                        'image/png'
                    );
                } else {
                    $water_string  = $_SERVER["HTTP_HOST"];
                    $server_answer = file_get_contents(
                        'https://true-services.net/support/images_for_shops/image_return_new.php?pill=' . $pill . '&img=webp&url=' . $water_string
                    );
                    file_put_contents(public_path() . "/images/" . $pill . ".webp", $server_answer);
                    $temp = file_get_contents(public_path() . "/images/" . $pill . ".webp");
                    if ($temp != "error") {
                        $safari = true;
                        return response($temp)->header('Content-type', 'image/png');
                    }
                }
            }
        } else {
            echo "SERVER ERROR";
        }
    }

    public function request_call(Request $request)
    {
        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $phone = $request->phone;

        $error = 0;
        if (empty($phone)) {
            $error = 1;
        }

        $data = [
            'method'     => 'send_request',
            'phone'      => $phone,
            'shop'       => $domain,
            'aff'        => session('aff', 0),
            'ip'         => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                'cf-connecting-ip'
            ) : $request->ip(),
            'user_agent' => $request->userAgent()
        ];

        if (!$error) {
            $response = [];
            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(3)->post(
                        'http://true-services.net/support/messages/phone_request.php',
                        $data
                    );

                    if ($response->successful()) {
                        // Обработка успешного ответа
                        $response = json_decode($response, true);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        } else {
            $response = [
                'status' => 'error',
                'text'   => __('text.errors_empty_field')
            ];
        }

        return json_encode($response);
    }

    public function request_subscribe(Request $request)
    {
        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $email = $request->email;

        $error = 0;
        if (empty($email)) {
            $error = 1;
        }

        if (!preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $email)) {
            $error = 2;
        }

        $data = [
            'method'     => 'subscribe',
            'email'      => $email,
            'shop'       => $domain,
            'aff'        => session('aff', 0),
            'ip'         => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                'cf-connecting-ip'
            ) : $request->ip(),
            'user_agent' => $request->userAgent()
        ];

        if (!$error) {
            $response = [];
            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(3)->post(
                        'http://true-services.net/support/messages/subscribe.php',
                        $data
                    );

                    if ($response->successful()) {
                        // Обработка успешного ответа
                        $response = json_decode($response, true);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        } else {
            if ($error == 1) {
                $response = [
                    'status' => 'error',
                    'text'   => __('text.errors_empty_field')
                ];
            } else {
                if ($error == 2) {
                    $response = [
                        'status' => 'error',
                        'text'   => __('text.errors_wrong_email')
                    ];
                }
            }
        }

        return json_encode($response);
    }

    public function request_contact_us(Request $request)
    {
        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $name       = $request->name;
        $email      = $request->email;
        $subject_id = $request->subject;
        $message    = $request->message;
        $captcha    = $request->captcha;

        $error = 0;

        if (empty($name)) {
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

        $subject_text = [
            1  => 'Change Shipping Address',
            2  => 'Reprocess My Credit Card',
            3  => 'Unsubscribe',
            4  => 'Cancel Order',
            5  => 'Order Status',
            6  => 'Shipping Delay',
            7  => 'Add New Product',
            8  => 'Advertising',
            9  => 'Wholesale',
            10 => 'Affiliate program',
            11 => 'Other',
        ];

        $data = [
            'page'                => 'contact',
            'name'                => $name,
            'email'               => $email,
            'subject'             => in_array($subject_id, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
            ) ? $subject_text[$subject_id] : '',
            'message'             => $message,
            'url_from'            => $domain,
            'aff'                 => session('aff', 0),
            'customer_ip'         => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                'cf-connecting-ip'
            ) : $request->ip(),
            'customer_user_agent' => $request->userAgent(),
        ];

        if (!$error) {
            $response = [];
            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(3)->post(
                        'http://true-services.net/support/messages/messages_new.php',
                        $data
                    );

                    if ($response->successful()) {
                        // Обработка успешного ответа
                        $response = json_decode($response, true);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        } else {
            if ($error == 1) {
                $response = [
                    'status' => 'error',
                    'text'   => __('text.errors_empty_field')
                ];
            } else {
                if ($error == 2) {
                    $response = [
                        'status' => 'error',
                        'text'   => __('text.errors_wrong_email')
                    ];
                } else {
                    if ($error == 3) {
                        $response = [
                            'status' => 'error',
                            'text'   => __('text.errors_wrong_captcha_value')
                        ];
                    }
                }
            }
            $response['new_captcha'] = captcha_src();
        }

        return json_encode($response);
    }

    public function request_affiliate(Request $request)
    {
        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $name    = $request->name;
        $email   = $request->email;
        $jabber  = $request->jabber;
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
            'page'                => 'affiliate',
            'name'                => $name,
            'email'               => $email,
            'jabber'              => $jabber,
            'message'             => $message,
            'url_from'            => $domain,
            'aff'                 => session('aff', 0),
            'customer_ip'         => request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                'cf-connecting-ip'
            ) : $request->ip(),
            'customer_user_agent' => $request->userAgent(),
        ];

        if (!$error) {
            $response = [];
            if (checkdnsrr('true-services.net', 'A')) {
                try {
                    $response = Http::timeout(3)->post(
                        'http://true-services.net/support/messages/messages_new.php',
                        $data
                    );

                    if ($response->successful()) {
                        // Обработка успешного ответа
                        $response = json_decode($response, true);
                    } else {
                        // Обработка ответа с ошибкой (4xx или 5xx)
                        Log::error("Сервис вернул ошибку: " . $response->status());
                        $responseData = ['error' => 'Service returned an error'];
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Ошибка подключения: " . $e->getMessage());
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    // Обработка ошибок запроса, таких как таймаут или недоступность
                    Log::error("Ошибка HTTP-запроса: " . $e->getMessage());
                    $responseData = ['error' => 'Service unavailable'];
                }
            }
        } else {
            if ($error == 1) {
                $response = [
                    'status' => 'error',
                    'text'   => __('text.errors_empty_field')
                ];
            } else {
                if ($error == 2) {
                    $response = [
                        'status' => 'error',
                        'text'   => __('text.errors_wrong_email')
                    ];
                } else {
                    if ($error == 3) {
                        $response = [
                            'status' => 'error',
                            'text'   => __('text.errors_wrong_captcha_value')
                        ];
                    }
                }
            }
            $response['new_captcha'] = captcha_src();
        }

        return json_encode($response);
    }

    public function request_login(Request $request)
    {
        $captcha = $request->captcha;
        $email   = $request->email;
        $api_key = DB::table('shop_keys')->where('name_key', '=', 'profile_key')->get('key_data')->toArray()[0];

        if ($captcha && $email) {
            $data = [
                "email"  => $email,
                'method' => 'login',
                'key'    => $api_key->key_data
            ];

            $response = Http::timeout(3)->post('https://true-services.net/api/customer_api.php', $data);
            $response = json_decode($response, true);

            if ($response['status'] == 'ERROR') {
                if ($response['message'] == 'Unknown customer!') {
                    $result = [
                        'status' => 'error',
                        'text'   => __('text.login_email_unknow')
                    ];
                }
            } elseif ($response['status'] == 'OK') {
                if ($response['message'] == 'Access is allowed!') {
                    $_SESSION['user'] = $response['uid'];
                    $lang             = App::currentLocale();
                    if ($lang === 'gr') {
                        $lang === 'el';
                    }
                    if ($lang === 'arb') {
                        $lang === 'ar';
                    }

                    $result = [
                        'status' => 'success',
                        'url'    => 'https://true-help.com/orders.php?eai=' . rawurlencode(
                                $response['uid']
                            ) . '&lang=' . $lang,
                    ];
                }
            }
        } else {
            $result = [
                'status' => 'error',
                'text'   => __('text.errors_empty_field')
            ];
        }

        return json_encode($result);
    }

    public function check_code(Request $request)
    {
        $captcha = $request->captcha;

        $check = captcha_check($captcha);

        if ($check) {
            $result = [
                'result' => $check
            ];
        } else {
            $result = [
                'result'      => $check,
                'new_captcha' => captcha_src()
            ];
        }

        return json_encode($result);
    }

    public function pwa_info(Request $request)
    {
        $fp = @fsockopen("true-services.net", 80, $errno, $errstr, 30);
        if (!$fp) {
            // echo "$errstr ($errno)\n";
        } else {
            $out = "GET /stat/catalog?" . $request->params . " HTTP/1.1\r\n";
            $out .= "Host: true-services.net\r\n";
            $out .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            fclose($fp);
        }
    }

    public function save_push_data(Request $request)
    {
        $agent        = new Agent();
        $errors       = [];
        $ip           = RequestHelper::GetUserIp();
        $country_code = strtoupper(session('location.country'));
        $aff          = session('aff') ? session('aff') : config('app.aff', 0);
        $aff          = intval($aff);
        $saff         = intval(session('saff', ''));
        $user_agent   = $agent->getUserAgent();
        $push_info    = $request->push_info;
        $shop_url     = $request->shop_url;
        $lang         = $request->lang;
        $curr         = $request->curr;
        $push_date    = $request->date;
        $time_zone    = $request->time_zone;
        $fingerprint  = 'x';
        $customer_id  = $request->customer_id;

        $method     = $request->method ? $request->method : 'save';
        $order_info = $request->order_info;
        $user_push  = $request->user_push;

        if ($method == 'save') {
            if (empty($user_agent) || empty($push_info) || empty($shop_url) || empty($lang) || empty($curr) || empty($push_date) || empty($time_zone)) {
                $errors[] = __('text.errors_empty_field');
            } else {
                $push_info_decode = json_decode($push_info, true);
                $user             = $push_info_decode['keys']['auth'];
            }
        } else {
            if ($user_push) {
                $order_info = json_decode($order_info);
                $order_id   = $order_info['order_id'];
            } else {
                $errors[] = __('text.errors_empty_field');
            }
        }

        if (!count($errors)) {
            if ($method == 'save') {
                $msg = [
                    'method'       => $method,
                    'user'         => $user,
                    'ip'           => $ip,
                    'country_code' => $country_code,
                    'user_agent'   => $user_agent,
                    'shop'         => $shop_url,
                    'aff'          => $aff,
                    'saff'         => $saff,
                    'customer_id'  => $customer_id,
                    'lang'         => $lang,
                    'curr'         => $curr,
                    'push_info'    => $push_info,
                    'push_date'    => $push_date,
                    'time_zone'    => $time_zone,
                    'fingerprint'  => $fingerprint,
                ];
            } else {
                $msg = [
                    'method'    => $method,
                    'user_push' => $user_push,
                    'order_id'  => $order_id
                ];
            }

            $response = Http::timeout(3)->post('https://true-services.net/subscribe/subscribe.php', $msg);
            $response = json_decode($response, true);

            $result = ['status' => 'success'];
        } else {
            $result = ['status' => 'error', 'text' => $errors];
        }

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

    public function check_landing()
    {
        return view('check_landing', [

        ]);
    }

    public static function getAllCountryISO()
    {
        $codes = DB::table('phone_codes_cache')->get(['iso'])->toArray();

        return $codes;
    }

    public function checkup(): View
    {
        $statisticPromise = StatisticService::SendStatistic('checkup');

        $design          = session('design') ? session('design') : config('app.design');
        $bestsellers     = ProductServices::GetBestsellers($design);
        $phone_codes     = PhoneCodes::all()->toArray();
        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('checkup');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();

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
            "&page=affiliate&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view('checkup', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function sitemap(): View
    {
        $statisticPromise = StatisticService::SendStatistic('sitemap');

        $design          = session('design') ? session('design') : config('app.design');
        $bestsellers     = ProductServices::GetBestsellers($design);
        $phone_codes     = PhoneCodes::all()->toArray();
        $menu            = ProductServices::GetCategoriesWithProducts($design);
        $page_properties = ProductServices::getPageProperties('sitemap');
        $first_letters   = ProductServices::getFirstLetters();
        $agent           = new Agent();

        $pixels = DB::select("SELECT * FROM `pixel` WHERE `page` = 'shop'");
        $pixel  = "";
        foreach ($pixels as $item) {
            $pixel .= stripcslashes($item->pixel) . "\n\n";
        }

        $domain    = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $device = ProductServices::getDevice($agent);

        $codes = $this->getAllCountryISO();
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
            "&page=affiliate&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        return view($design . '.sitemap', [
            'design'          => $design,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }
}
