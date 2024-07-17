<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryDesc;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductDesc;
use App\Models\ProductDisease;
use App\Models\ProductPackaging;
use App\Models\ProductTypeDesc;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductServices
{
    public function GetBestsellers() {
        $products_desc = $this->GetProductDesc(Language::$languages[App::currentLocale()]);
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
        $products_desc = $this->GetProductDesc(Language::$languages[App::currentLocale()]);
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
            unset($product);

            $categories[$category->id]['name'] = $category_desc[$category->id];
            $categories[$category->id]['products'] = $products;
        }

        return $categories;
    }

    public function GetProductDesc($language_id, $url = '')
    {
        if(empty($url))
        {
            $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();
        }
        else
        {
            $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->where('url', '=', $url)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();
        }

        $products_desc = [];
        foreach($products_desc_raw as $key => $p)
        {
            $products_desc[$key] = $p[0];
            if(!empty($url))
            {
                return $p[0];
            }
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
        $products_desc = $this->GetProductDesc(Language::$languages[App::currentLocale()]);
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

    public function GetProductInfoByUrl($url)
    {
        $language_id = Language::$languages[App::currentLocale()];
        $products_desc = $this->GetProductDesc($language_id, $url);

        $product = Product::query()
            ->where('id', '=', $products_desc['product_id'])
            ->where('is_showed', '=', 1)
            ->with('category.category_desc')
            ->get(['id', 'image', 'aktiv', 'sinonim', 'product_info_file_path']);

        #region Category
        $categories = [];
        foreach($product[0]->category as $category)
        {
            $names = $category->category_desc->where('language_id', '=', $language_id);
            foreach($names as $n)
            {
                $name = $n['name'];
            }
            $categories[] = $name;
        }
        #endregion

        #region Analogs
        $analogs = DB::select('SELECT pd.name, pd.url
        FROM product_analog pa
        JOIN product_desc pd ON pd.product_id = pa.analog_id
        JOIN product p ON p.id = pa.analog_id
        WHERE pa.product_id = ? AND pd.language_id = ? AND p.is_showed = 1',
        [$products_desc['product_id'], $language_id]);
        #endregion

        #region Product Disease
        $product_disease = [];
        $product_diseases = ProductDisease::query()
            ->where('product_id', '=', $products_desc['product_id'])
            ->where('language_id', '=', $language_id)
            ->get('disease')
            ->toArray();

        foreach($product_diseases as $disease)
        {
            $product_disease[] = $disease['disease'];
        }
        #endregion

        #region Packaging
        $packs = ProductPackaging::query()
            ->where('product_id', '=', $products_desc['product_id'])
            ->where('price', '!=', 0)
            ->where('is_showed', '=', 1)
            ->orderBy('ord')
            ->get()
            ->groupBy('dosage')
            ->toArray();

        $product_type = 1; //defualt pills
        foreach($packs as &$pack)
        {

            $max_pill_price = 0;
            foreach($pack as $p)
            {
                $product_type = $p['type_id'];
                $max_pill_price = ($p['price'] / $p['num']) > $max_pill_price ? round($p['price'] / $p['num'], 2) : $max_pill_price;
                // break;
            }
            // dd();
            $pack['max_pill_price'] = $max_pill_price;
        }
        unset($pack);

        krsort($packs, SORT_NUMERIC); //сортировка упаковок по дозации
        #endregion

        #region Product Type
        $type = ProductTypeDesc::query()
        ->where('type_id', '=', $product_type) //$product_type получен в регионе Packaging
        ->where('language_id', '=', $language_id)
        ->first('name')->name;
        #endregion

        $product = $product->toArray()[0];
        unset($product['category']);
        $product['categories'] = $categories;
        $product['name'] = $products_desc['name'];
        $product['desc'] = $products_desc['desc'];
        $product['aktiv'] = explode(',', str_replace(' ', '', ucwords($product['aktiv'])));
        $product['disease'] = $product_disease;
        $product['analog'] = json_decode(json_encode($analogs), true);
        $product['sinonim'] = explode("\n", str_replace("\u{FEFF}", '', $product['sinonim']));
        $path = public_path() . '\languages\\' . App::currentLocale() . '\tablets_descriptions\\' . str_replace('/', '\\', $product['product_info_file_path']);
        $product['full_desc'] = File::exists($path) ? File::get($path) : '';
        $product['packs'] = $packs;
        $product['type'] = $type;

        dump($product);
        return $product;
    }

}
