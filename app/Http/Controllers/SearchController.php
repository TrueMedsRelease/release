<?php

namespace App\Http\Controllers;

use App\Models\ProductSearch;
use App\Services\ProductServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Services\CurrencyServices;
use App\Services\LanguageServices;

class SearchController extends Controller
{

    public function search_product()
    {
        $search_text = $_POST['search_text'];
        return redirect(route('search.search_result', [$search_text]));
    }

    public static function search_result($search_text) : View
    {
        $design = config('app.design');
        $languages = LanguageServices::getAllLanguages();
        $currencies = CurrencyServices::getAllCurrencies();
        $bestsellers = ProductServices::GetBestsellers($design);
        $menu = ProductServices::GetCategoriesWithProducts($design);
        $products = ProductServices::SearchProduct($search_text, $design);

        return view($design . '.search_result', [
            'design' => $design,
            'search_text' => $search_text,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
            'languages' => $languages,
            'currencies' => $currencies
        ]);
    }

    public function search_autocomplete(Request $request)
    {
        $search_text = $request->query('q');
        $design = config('app.design');
        $products = ProductServices::SearchProduct($search_text, $design);

        $tips = '';
        foreach($products as $product)
        {
            $tips .= $product['name'] . '||' . $product['url'] . "\n";
        }

        return $tips;
    }

}
