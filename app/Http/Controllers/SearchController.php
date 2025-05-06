<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\PhoneCodes;
use App\Services\ProductServices;
use App\Services\StatisticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Phattarachai\LaravelMobileDetect\Agent;

class SearchController extends Controller
{

    public function search_product()
    {
        $search_text = $_POST['search_text'];
        return redirect(route('search.search_result', [$search_text]));
    }

    public static function search_result($search_text)
    {
        $statisticPromise = StatisticService::SendStatistic('search');

        $design      = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        $menu        = ProductServices::GetCategoriesWithProducts($design);
        $products    = ProductServices::SearchProduct($search_text, false, $design);

        if (!is_null($statisticPromise)) {
            $statisticPromise->wait();
        }

        if (count($products) == 1) {
            return redirect(route('home.product', $products[0]['url']));
        }

        $phone_codes     = PhoneCodes::all()->toArray();
        $page_properties = ProductServices::getPageProperties('search');
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
            "&page=active&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . request()->headers->get('cf-connecting-ip') ? request()->headers->get(
                'cf-connecting-ip'
            ) : request()->ip();

        return view($design . '.search_result', [
            'design'          => $design,
            'search_text'     => $search_text,
            'bestsellers'     => $bestsellers,
            'menu'            => $menu,
            'products'        => $products,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'cur_category'    => '',
            'agent'           => $agent,
            'pixel'           => $pixel,
            'first_letters'   => $first_letters,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function search_for_aff()
    {
        if (!empty(request('search'))) {
            return $this->search_result(request('search'));
        } else {
            return redirect(route('home.index'));
        }
    }

    public function search_autocomplete(Request $request)
    {
        $search_text = $request->query('q');
        $design      = session('design') ? session('design') : config('app.design');
        $products    = ProductServices::SearchProductAutocomplete($search_text, $design);
        $sinonim     = ProductServices::SearchSinonim($search_text);
        $page        = ProductServices::SearchPageTitle($search_text);
        $category    = ProductServices::SearchCategory($search_text);
        $disease     = ProductServices::SearchDisease($search_text);
        $active      = ProductServices::SearchActive($search_text);

        $tips = '';
        // foreach($products as $product)
        // {
        //     $tips .= $product['name'] . '||' . $product['url'] . "\n";
        // }

        if (!empty($products)) {
            $tips .= $products;
        }

        if (!empty($page)) {
            $tips .= $page;
        }

        if (!empty($category)) {
            $tips .= $category;
        }

        if (!empty($disease)) {
            $tips .= $disease;
        }

        if (!empty($active)) {
            $tips .= $active;
        }

        if (!empty($sinonim)) {
            $tips .= $sinonim;
        }

        if (!$tips) {
            $tips = __('text.search_nothing') . "||search/" . $search_text;
        }

        return $tips;
    }

}
