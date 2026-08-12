<?php

namespace App\Http\Controllers;

use App\Helpers\RequestHelper;
use App\Models\Currency;
use App\Models\Language;
use App\Models\PhoneCodes;
use App\Services\CatalogCursorService;
use App\Services\ProductServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Phattarachai\LaravelMobileDetect\Agent;

class CatalogController
{
    private const PER_PAGE = 12;

    public function index(Request $request)
    {
        $design = session('design') ? session('design') : config('app.design');

        if ($design !== 'design_17') {
            return redirect()->route('home.index');
        }

        $search   = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));

        $result = app(CatalogCursorService::class)->paginate(
            $design,
            $search,
            $category,
            null,
            self::PER_PAGE
        );

        $total   = $result['total'];
        $products  = $result['products'];
        $hasMore   = $result['has_more'];
        $nextCursor = $result['next_cursor'];

        $menu = ProductServices::GetCategoriesWithProducts($design);

        $phone_codes = PhoneCodes::all()->toArray();
        $agent       = new Agent();

        $page_properties = (object)[
            'title'       => 'Catalog',
            'keyword'     => 'Catalog',
            'description' => 'Catalog',
        ];

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

        $codes = HomeController::getAllCountryISO();
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
            "&page=catalog&device=" . $device .
            "&timestamp=" . time() .
            "&user_ip=" . RequestHelper::GetUserIp();

        return view('design_17.catalog', [
            'design'          => $design,
            'menu'            => $menu,
            'products'        => $products,
            'per_page'        => self::PER_PAGE,
            'total'           => $total,
            'has_more'        => $hasMore,
            'next_cursor'     => $nextCursor,
            'search'          => $search,
            'category'        => $category,
            'phone_codes'     => $phone_codes,
            'page_properties' => $page_properties,
            'agent'           => $agent,
            'Language'        => Language::class,
            'Currency'        => Currency::class,
            'pixel'           => $pixel,
            'domain'          => $domain,
            'web_statistic'   => $web_statistic,
            'codes'           => json_encode($codes),
        ]);
    }

    public function load(Request $request)
    {
        $design = session('design') ? session('design') : config('app.design');

        if ($design !== 'design_17') {
            return response()->json([
                'status'   => 'redirect',
                'redirect' => route('home.index'),
            ]);
        }

        $cursor   = $request->has('cursor') ? trim((string) $request->input('cursor')) : null;
        $search   = trim((string) $request->input('search', ''));
        $category = trim((string) $request->input('category', ''));

        $result = app(CatalogCursorService::class)->paginate(
            $design,
            $search,
            $category,
            $cursor,
            self::PER_PAGE
        );

        $total      = $result['total'];
        $slice      = $result['products'];
        $hasMore    = $result['has_more'];
        $nextCursor = $result['next_cursor'];

        $html = '';
        if (!empty($slice)) {
            $html = view('design_17.ajax.catalog_cards', [
                'design'   => $design,
                'products' => $slice,
                'Currency' => Currency::class,
            ])->render();
        }

        return response()->json([
            'status'      => 'success',
            'html'        => $html,
            'has_more'    => $hasMore,
            'total'       => $total,
            'next_cursor' => $nextCursor,
        ]);
    }
}
