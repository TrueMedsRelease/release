<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\ProductSearch;
use App\Services\ProductServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SearchController extends Controller
{

    public function search_product()
    {
        $search_text = $_POST['search_text'];
        return redirect(route('search.search_result', [$search_text]));
    }

    public static function search_result($search_text) : View
    {
        $bestsellers = ProductServices::GetBestsellers();
        $menu = ProductServices::GetCategoriesWithProducts();
        $products = ProductServices::SearchProduct($search_text);

        $design = config('app.design');
        return view($design . '.search_result', [
            'design' => $design,
            'search_text' => $search_text,
            'bestsellers' => $bestsellers,
            'menu' => $menu,
            'products' => $products,
            'Currency' => Currency::class,
            'Language' => Language::class,
        ]);
    }

    public function search_autocomplete(Request $request)
    {
        $search_text = $request->query('q');
        $products = ProductServices::SearchProduct($search_text);

        $tips = '';
        foreach($products as $product)
        {
            $tips .= $product['name'] . '||' . $product['url'] . "\n";
        }

        return $tips;
    }

}
