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
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{

    public function search_product()
    {
        $search_text = isset($_POST['search_text']) ? $_POST['search_text'] : '';
        return redirect(route('search.search_result', [$search_text]));
    }

    public function search_chat(Request $request)
    {
        $search_text = trim((string) $request->input('search_text', ''));
        $design      = session('design') ? session('design') : config('app.design');

        if ($search_text === '') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Empty search text',
            ], 422);
        }

        if ($design !== 'design_17') {
            return response()->json([
                'status'   => 'redirect',
                'redirect' => route('search.search_result', [$search_text]),
            ]);
        }

        self::rememberSearchHistory($search_text);

        $products    = ProductServices::SearchProduct($search_text, false, $design);
        $bestsellers = count($products) === 0 ? ProductServices::GetBestsellers($design) : [];

        return response()->json([
            'status' => 'success',
            'count'  => count($products),
            'html'   => view($design . '.ajax.chat_search_result', [
                'design'      => $design,
                'search_text' => $search_text,
                'products'    => $products,
                'bestsellers' => $bestsellers,
                'Currency'    => Currency::class,
            ])->render(),
        ]);
    }

    public function search_chat_suggest(Request $request)
    {
        $search_text = trim((string) $request->query('q', ''));
        $design      = session('design') ? session('design') : config('app.design');

        if ($design !== 'design_17') {
            return response()->json([
                'status' => 'disabled',
            ]);
        }

        if (mb_strlen($search_text) < 2) {
            return response()->json([
                'status' => 'empty',
                'html'   => '',
                'count'  => 0,
            ]);
        }

        $tips  = self::buildAutocompleteTips($search_text, $design);
        $items = self::parseAutocompleteTips($tips, $search_text, 7);

        return response()->json([
            'status' => 'success',
            'count'  => count($items),
            'html'   => view($design . '.ajax.chat_search_suggest', [
                'search_text' => $search_text,
                'items'       => $items,
            ])->render(),
        ]);
    }

    public static function search_result($search_text)
    {
        $design      = session('design') ? session('design') : config('app.design');

        if (in_array($design, ['design_7', 'design_8'])) {
            if (env('APP_ERROR_PAGE')) {
                return response()->view('404', ['design' => session('design', config('app.design'))], 404);
            } else {
                return redirect(route('home.index'));
            }
        }

        // $statisticPromise = StatisticService::SendStatistic('search');

        self::rememberSearchHistory((string) $search_text);

        $bestsellers = ProductServices::GetBestsellers($design);
        $menu        = ProductServices::GetCategoriesWithProducts($design);
        $products    = ProductServices::SearchProduct($search_text, false, $design);

        // if (!is_null($statisticPromise)) {
        //     $statisticPromise->wait();
        // }

        if (count($products) == 1 && $design !== 'design_17') {
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
            if (env('APP_ERROR_PAGE')) {
                return response()->view('404', ['design' => session('design', config('app.design'))], 404);
            } else {
                return redirect(route('home.index'));
            }
        }
    }

    private static function rememberSearchHistory(string $search_text): void
    {
        $keywords = ['plavix', 'avapro', 'stilnox', 'ambien', 'zolpidem', 'plaquenil'];

        $matched_keywords = [];
        foreach ($keywords as $keyword) {
            if (stripos(strtolower($search_text), $keyword) !== false) {
                $matched_keywords[] = $keyword;
            }
        }

        $history = session()->get('search_history', []);

        foreach ($matched_keywords as $word) {
            $history[$word] = true;
        }

        session()->put('search_history', $history);

        if (count($history) < 2) {
            return;
        }

        $ip        = request()->headers->get('cf-connecting-ip') ?? request()->ip();
        $domain    = request()->getHost();
        $userAgent = request()->userAgent();
        $log_data  = "[" . now() . "] || Domain: $domain || IP: $ip || Keywords: $search_text || User Agent: $userAgent" . PHP_EOL;

        if (!@is_writable('/var/www')) {
            Log::error("Directory {/var/www} is not writable.");
            return;
        }

        file_put_contents('/var/www/search_audit.log', $log_data, FILE_APPEND);
    }

    private static function buildAutocompleteTips(string $search_text, string $design): string
    {
        $products = ProductServices::SearchProductAutocomplete($search_text, $design);
        $page     = ProductServices::SearchPageTitle($search_text);
        $category = ProductServices::SearchCategory($search_text, $design);
        $disease  = ProductServices::SearchDisease($search_text, $design);
        $active   = ProductServices::SearchActive($search_text, $design);
        $sinonim  = ProductServices::SearchSinonim($search_text, $design);

        $tips = '';

        foreach ([$products, $page, $category, $disease, $active, $sinonim] as $part) {
            if (!empty($part)) {
                $tips .= $part;
            }
        }

        return $tips;
    }

    private static function parseAutocompleteTips(string $tips, string $search_text, int $limit = 7): array
    {
        $items   = [];
        $used    = [];
        $nothing = mb_strtolower((string) __('text.search_nothing'));

        foreach (preg_split('/\r\n|\r|\n/', $tips) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            [$title, $url] = array_pad(explode('||', $line, 2), 2, '');

            $title = trim(strip_tags($title));
            $url   = trim($url);

            if ($title === '' || mb_strtolower($title) === $nothing) {
                continue;
            }

            $key = mb_strtolower($title);

            if (isset($used[$key])) {
                continue;
            }

            $used[$key] = true;
            $items[]    = [
                'title' => $title,
                'url'   => ltrim($url !== '' ? $url : 'search/' . $search_text, '/'),
            ];

            if (count($items) >= $limit) {
                break;
            }
        }

        return $items;
    }

    public function search_autocomplete(Request $request)
    {
        $search_text = (string) $request->query('q', '');
        $design      = session('design') ? session('design') : config('app.design');
        $tips        = self::buildAutocompleteTips($search_text, $design);

        if (!$tips) {
            $tips = __('text.search_nothing') . "||search/" . $search_text;
        }

        return $tips;
    }

}
