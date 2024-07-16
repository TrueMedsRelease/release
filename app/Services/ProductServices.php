<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryDesc;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductDesc;
use App\Models\ProductPackaging;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ProductServices
{
    public function GetBestsellers() {
        $products_desc = $this->GetAllProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = $this->GetAllProductPillPrice();

        $products = Product::query()
            ->where('is_showed_on_main', '=', 1)
            ->where('is_showed', '=', 1)
            ->orderBy('main_order', 'asc')
            ->get(['id', 'image', 'aktiv'])
            ->toArray();


        for($i = 0; $i < count($products); $i++)
        {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['price'] = $product_price[$products[$i]['id']];
        }

        return $products;
    }

    public function GetAllCategoriesWithProducts()
    {
        $category_desc = $this->GetAllCategoryDesc(Language::$languages[App::currentLocale()]);
        $products_desc = $this->GetAllProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = $this->GetAllProductPillPrice();

        $categories_raw = Category::query()
            ->with(['product'])
            ->orderBy('ord')
            ->get();

        $categories = [];
        foreach($categories_raw as $category)
        {
            $products = $category->product->where('is_showed', '=', 1)->sortBy('menu_order')->toArray();
            foreach($products as &$product)
            {
                $product['price'] = $product_price[$product['id']];
                $product['name'] = $products_desc[$product['id']]['name'];
            }
            $categories[$category->id]['name'] = $category_desc[$category->id];
            $categories[$category->id]['products'] = $products;
        }

        return $categories;
    }

    public function GetAllProductDesc($language_id)
    {
        $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->get(['product_id', 'name', 'desc'])->groupBy('product_id')->toArray();

        $products_desc = [];
        foreach($products_desc_raw as $key => $p)
        {
            $products_desc[$key] = $p[0];
        }

        return $products_desc;
    }

    public function GetAllCategoryDesc($language_id)
    {
        $category_desc_raw = CategoryDesc::query()->where('language_id', '=', $language_id)->get(['category_id', 'name'])->groupBy('category_id')->toArray();
        $category_desc = [];
        foreach($category_desc_raw as $key => $p)
        {
            $category_desc[$key] = $p[0]['name'];
        }

        return $category_desc;
    }

    public function GetAllProductPillPrice()
    {

        $product = DB::select('SELECT product_id, MIN(`price` / `num`) as min FROM product_packaging WHERE is_showed = 1 AND price != 0 GROUP BY product_id');
        $product_price = [];
        foreach($product as $p)
        {
            $product_price[$p->product_id] = round($p->min, 2);
        }

        return $product_price;
    }

    public function GetProductByFirstLetter($letter)
    {
        $products_desc = $this->GetAllProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = $this->GetAllProductPillPrice();

        $products = Product::query()
            ->where('is_showed', '=', 1)
            ->where('first_letter', '=', $letter)
            ->orderBy('main_order', 'asc')
            ->get(['id', 'image', 'aktiv'])
            ->toArray();


        for($i = 0; $i < count($products); $i++)
        {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['price'] = $product_price[$products[$i]['id']];
        }

        return $products;
    }

}