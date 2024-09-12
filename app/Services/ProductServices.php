<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryDesc;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductDesc;
use App\Models\ProductDisease;
use App\Models\ProductPackaging;
use App\Models\ProductSearch;
use App\Models\ProductTypeDesc;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductServices
{
    public static function GetBestsellers($design)
    {
        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);

        if ($design == 'design_5') {
            $products = Product::query()
                ->where('product.is_showed_on_main', '=', 1)
                ->where('product.is_showed', '=', 1)
                ->where('product_category.category_id', '=', 14)
                ->join('product_category', 'product.id', '=', 'product_category.product_id')
                ->orderBy('product.main_order', 'asc')
                ->get(['product.id', 'product.image', 'product.aktiv'])
                ->toArray();

        } else {
            $products = Product::query()
                ->where('is_showed_on_main', '=', 1)
                ->where('is_showed', '=', 1)
                ->orderBy('main_order', 'asc')
                ->get(['id', 'image', 'aktiv'])
                ->toArray();
        }


        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', str_replace("\r\n", '', str_replace(' ', '', $products[$i]['aktiv'])));
            $products[$i]['price'] = $product_price[$products[$i]['id']];
        }

        return $products;
    }

    public static function GetCategoriesWithProducts($design, $url = '')
    {
        $category_desc = self::GetAllCategoryDesc(Language::$languages[App::currentLocale()], $design);
        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);

        if(empty($url))
        {
            if ($design == 'design_5') {
                $categories_raw = Category::query()
                ->whereIn('id', [13, 14])
                ->with(['product'])
                ->orderBy('ord')
                ->get();
            } else {
                $categories_raw = Category::query()
                ->with(['product'])
                ->orderBy('ord')
                ->get();
            }
        }
        else
        {
            if ($design == 'design_5') {
                $categories_raw = Category::query()
                    ->where('url', '=', $url)
                    ->whereIn('id', [13, 14])
                    ->with(['product'])
                    ->orderBy('ord')
                    ->get();
            } else {
                $categories_raw = Category::query()
                    ->where('url', '=', $url)
                    ->with(['product'])
                    ->orderBy('ord')
                    ->get();
            }
        }

        $categories = [];
        foreach($categories_raw as $category)
        {
            // if ($design == 'design_5') {
            //     $products = $category->product->where('is_showed', '=', 1)->where('category_id', '=', 14)->sortBy('menu_order')->toArray();
            // } else {
                $products = $category->product->where('is_showed', '=', 1)->sortBy('menu_order')->toArray();
            // }

            foreach($products as &$product)
            {
                $product['price'] = $product_price[$product['id']];
                $product['name'] = $products_desc[$product['id']]['name'];
                $product['desc'] = $products_desc[$product['id']]['desc'];
                $product['url'] = $products_desc[$product['id']]['url'];
                $product['aktiv'] = explode(',', str_replace("\r\n", '', str_replace(' ', '', $product['aktiv'])));
            }
            unset($product);

            $categories[$category->id]['url'] = $category->url;
            $categories[$category->id]['name'] = $category_desc[$category->id];
            $categories[$category->id]['products'] = $products;
        }

        return $categories;
    }

    public static function GetProductDesc($language_id, $url = '')
    {
        if(empty($url))
        {
            $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();
        }
        else
        {
            $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->where('url', '=', $url)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();

            if (!$products_desc_raw || empty($products_desc_raw)) {
                $product_id = DB::select("SELECT p.id FROM product p INNER JOIN product_category pc ON pc.product_id = p.id INNER JOIN category ca ON ca.id = pc.category_id WHERE ca.is_showed = 1 AND p.sinonim LIKE '%{$url}%' AND p.is_showed = 1");
                $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->where('product_id', '=', $product_id[0]->id)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();

                foreach($products_desc_raw as $key => $product) {
                    $products_desc_raw[$key][0]['name'] = ucfirst($url) . ' (' . __('text.product_other_name') . $product[0]['name'] . ')';
                }
            }
        }

        $products_desc = [];
        foreach ($products_desc_raw as $key => $p) {
            $products_desc[$key] = $p[0];
            if (!empty($url)) {
                return $p[0];
            }
        }

        return $products_desc;
    }

    public static function GetAllCategoryDesc($language_id, $design)
    {
        // if ($design == 'design_5') {
        //     $category_desc_raw = CategoryDesc::query()->where('language_id', '=', $language_id)->where('category_id', '=', 14)->get(['category_id', 'name'])->groupBy('category_id')->toArray();
        // } else {
            $category_desc_raw = CategoryDesc::query()->where('language_id', '=', $language_id)->get(['category_id', 'name'])->groupBy('category_id')->toArray();
        // }

        $category_desc = [];
        foreach ($category_desc_raw as $key => $p) {
            $category_desc[$key] = $p[0]['name'];
        }

        return $category_desc;
    }

    public static function GetAllProductPillPrice($design)
    {
        // if ($design == 'design_5') {
        //     $product = DB::select('SELECT product_id, MIN(`price` / `num`) as min FROM product_packaging WHERE is_showed = 1 AND price != 0 AND category_id = 14 GROUP BY product_id');
        // } else {
            $product = DB::select('SELECT product_id, MIN(`price` / `num`) as min FROM product_packaging WHERE is_showed = 1 AND price != 0 GROUP BY product_id');
        // }


        $product_price = [];
        foreach ($product as $p) {
            $product_price[$p->product_id] = round($p->min, 2);
        }

        return $product_price;
    }

    public static function GetProductByFirstLetter($letter, $design)
    {
        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);

        // if ($design == 'design_5') {
        //     $products = Product::query()
        //         ->where('is_showed', '=', 1)
        //         ->where('first_letter', '=', $letter)
        //         ->where('category_id', '=', 14)
        //         ->orderBy('main_order', 'asc')
        //         ->get(['id', 'image', 'aktiv'])
        //         ->toArray();

        // } else {
            $products = Product::query()
                ->where('is_showed', '=', 1)
                ->where('first_letter', '=', $letter)
                ->orderBy('main_order', 'asc')
                ->get(['id', 'image', 'aktiv'])
                ->toArray();
        // }

        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', str_replace("\r\n", '', str_replace(' ', '', $products[$i]['aktiv'])));
            $products[$i]['price'] = $product_price[$products[$i]['id']];
        }

        return $products;
    }

    public static function GetProductByDisease($disease, $design)
    {
        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);
        $disease = str_replace('-', ' ', $disease);

        // if ($design == 'design_5') {
        //     $diseases = DB::select('SELECT * FROM product_disease WHERE language_id = ? AND disease = ? AND category_id = ?', [Language::$languages[App::currentLocale()], $disease, 14]);
        // } else {
            $diseases = DB::select('SELECT * FROM product_disease WHERE language_id = ? AND disease = ?', [Language::$languages[App::currentLocale()], $disease]);
        // }

        $product_id = [];
        foreach ($diseases as $item) {
            $product_id[] = $item->product_id;
        }

        // if ($design == 'design_5') {
        //     $products = Product::query()
        //         ->where('is_showed', '=', 1)
        //         ->where('category_id', '=', 14)
        //         ->whereIn('id', $product_id)
        //         ->orderBy('main_order', 'asc')
        //         ->get(['id', 'image', 'aktiv'])
        //         ->toArray();

        // } else {
            $products = Product::query()
                ->where('is_showed', '=', 1)
                ->whereIn('id', $product_id)
                ->orderBy('main_order', 'asc')
                ->get(['id', 'image', 'aktiv'])
                ->toArray();
        // }

        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', str_replace("\r\n", '', str_replace(' ', '', $products[$i]['aktiv'])));
            $products[$i]['price'] = $product_price[$products[$i]['id']];
        }

        return $products;
    }

    public static function GetProductByActive($active, $design)
    {
        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);

        // if ($design == 'design_5') {
        //     $products = Product::query()
        //     ->where('is_showed', '=', 1)
        //     ->where('aktiv', 'LIKE', "%$active%")
        //     ->where('category_id', 14)
        //     ->orderBy('main_order', 'asc')
        //     // ->orderBy('image', 'asc')
        //     ->get(['id', 'image', 'aktiv'])
        //     ->toArray();
        // } else {
            $products = Product::query()
            ->where('is_showed', '=', 1)
            ->where('aktiv', 'LIKE', "%$active%")
            ->orderBy('main_order', 'asc')
            // ->orderBy('image', 'asc')
            ->get(['id', 'image', 'aktiv'])
            ->toArray();
        // }

        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', str_replace("\r\n", '', str_replace(' ', '', $products[$i]['aktiv'])));
            $products[$i]['price'] = $product_price[$products[$i]['id']];
        }

        return $products;
    }

    public static function GetProductInfoByUrl($url, $design)
    {
        $language_id = Language::$languages[App::currentLocale()];
        $products_desc = self::GetProductDesc($language_id, $url);

        $product = Product::query()
            ->where('id', '=', $products_desc['product_id'])
            ->where('is_showed', '=', 1)
            ->with('category.category_desc')
            ->get(['id', 'image', 'aktiv', 'sinonim', 'product_info_file_path']);

        #region Category
        $categories = [];
        foreach ($product[0]->category as $category) {
            $names = $category->category_desc->where('language_id', '=', $language_id);
            foreach ($names as $n) {
                $name = $n['name'];
            }
            $categories[] = ['name' => $name, 'url' => $category->url];
        }
        #endregion

        #region Analogs
        $analogs = DB::select(
            'SELECT pd.name, pd.url
        FROM product_analog pa
        JOIN product_desc pd ON pd.product_id = pa.analog_id
        JOIN product p ON p.id = pa.analog_id
        WHERE pa.product_id = ? AND pd.language_id = ? AND p.is_showed = 1',
            [$products_desc['product_id'], $language_id]
        );
        #endregion

        #region Product Disease
        $product_disease = [];
        $product_diseases = ProductDisease::query()
            ->where('product_id', '=', $products_desc['product_id'])
            ->where('language_id', '=', $language_id)
            ->get('disease')
            ->toArray();

        foreach ($product_diseases as $disease) {
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
        foreach ($packs as &$pack) {

            $max_pill_price = 0;
            foreach ($pack as $p) {
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

        $sinonims = DB::select("SELECT p.sinonim from product p WHERE p.id = ?", [$products_desc['product_id']]);
        $sinonims = preg_replace('/\b(\w.+)\b/', '$0/', $sinonims[0]->sinonim);
        $sinonims = explode('/', $sinonims);
        if ($sinonims[0] == "﻿" || $sinonims[0] == "")
        for ($i = 0; $i < count($sinonims); $i++)
            if ($i+1 != count($sinonims))
                $sinonims[$i] = $sinonims[$i+1];
        if ($sinonims[0] != "﻿" || $sinonims[0] != "")
        for ($i = 0; $i < count($sinonims); $i++)
            $sinonims[$i] = $sinonims[$i];

        $sinonims_new = [];
        foreach($sinonims as $ps) {
            if ($ps != "") {
                $sinonim_name = trim(str_replace('-', ' ', str_replace("\u{FEFF}", '', $ps)));
                $sinonim_url = strtolower(htmlentities(trim(str_replace('&', '-', (str_replace(' ', '-', str_replace("\u{FEFF}", '', $ps)))))));
                $sinonims_new[] = array (
                    "name" => $sinonim_name,
                    "url" => $sinonim_url
                );
            }
        }

        $product['sinonim'] = [];

        if ($sinonims_new) {
            $product['sinonim'] = array_merge($product['sinonim'], $sinonims_new);
        } else {
            $product['sinonim'] = '';
        }

        $rec_name = 'none';
        $rec_url = '';
        if ($products_desc['name'] === 'Viagra') {
            $rec_name = 'Viagra Extra Dosage';
            $rec_url = 'viagra-extra-dosage';
        }
        if ($products_desc['name'] === 'Cialis') {
            $rec_name = 'Cialis Extra Dosage';
            $rec_url = 'cialis-extra-dosage';
        }
        if ($products_desc['name'] === 'Levitra') {
            $rec_name = 'Levitra Extra Dosage';
            $rec_url = 'levitra-extra-dosage';
        }
        if ($products_desc['name'] === 'Super Viagra') {
            $rec_name = 'Extra Super Viagra';
            $rec_url = 'extra-super-viagra';
        }
        if ($products_desc['name'] === 'Super Cialis') {
            $rec_name = 'Extra Super Cialis';
            $rec_url = 'extra-super-cialis';
        }
        if ($products_desc['name'] === 'Super Levitra') {
            $rec_name = 'Extra Super Levitra';
            $rec_url = 'extra-super-levitra';
        }
        if ($products_desc['name'] === 'Super Avana') {
            $rec_name = 'Extra Super Avana';
            $rec_url = 'extra-super-avana';
        }


        $product['categories'] = $categories;
        $product['name'] = $products_desc['name'];
        $product['desc'] = $products_desc['desc'];
        $product['aktiv'] = explode(',', str_replace(' ', '', ucwords($product['aktiv'])));
        $product['disease'] = $product_disease;
        $product['analog'] = json_decode(json_encode($analogs), true);
        $product['sinonim'] = $product['sinonim'];
        $path = public_path() . '/languages/' . App::currentLocale() . '/tablets_descriptions/' . $product['product_info_file_path'];
        $product['full_desc'] = File::exists($path) ? File::get($path) : '';
        $product['packs'] = $packs;
        $product['type'] = $type;
        $product['rec_name'] = $rec_name;
        $product['rec_url'] = $rec_url;

        return $product;
    }

    public static function GetMaxPriceForPill($product_id, $dosage)
    {
        $packs = ProductPackaging::query()
            ->where('product_id', '=', $product_id)
            ->where('dosage', '=', $dosage)
            ->where('price', '!=', 0)
            ->where('is_showed', '=', 1)
            ->get()
            ->toArray();

        $max_pill_price = 0;
        foreach ($packs as $pack) {
            $max_pill_price = ($pack['price'] / $pack['num']) > $max_pill_price ? round($pack['price'] / $pack['num'], 2) : $max_pill_price;
        }

        return $max_pill_price;
    }

    public static function GetUpgradePack($pack_id)
    {
        $product_pack = ProductPackaging::query()->find($pack_id);

        $upgrade_pack = ProductPackaging::query()
            ->where('product_id', '=', $product_pack->product_id)
            ->where('dosage', '=', $product_pack->dosage)
            ->where('price', '!=', 0)
            ->where('is_showed', '=', 1)
            ->where('num', '>', $product_pack->num)
            ->orderBy('num', 'asc')
            ->limit(1)
            ->get()
            ->toArray();

        if (!empty($upgrade_pack)) {
            $upgrade_pack = $upgrade_pack[0];

            return [
                'pack_id' => $upgrade_pack['id'],
                'price' => $upgrade_pack['price'],
                'num' => $upgrade_pack['num']
            ];
        } else {
            return [];
        }
    }

    public static function SearchProduct($search_text, $design)
    {
        if (str_contains($search_text, ' ')) {
            $search_text = '(' . $search_text . ')';
        }

        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);
        $product_ids = ProductSearch::whereFullText('keyword', $search_text . '*', ['mode' => 'boolean'])
            ->distinct()
            ->get(['product_id'])
            ->toArray();

        $product_id = [];
        foreach ($product_ids as $item) {
            $product_id[] = $item['product_id'];
        }

        // if ($design == 'design_5') {
        //     $products = Product::query()
        //         ->where('is_showed', '=', 1)
        //         ->where('caterogy_id', '=', 14)
        //         ->whereIn('id', $product_id)
        //         ->orderBy('main_order', 'asc')
        //         ->get(['id', 'image', 'aktiv'])
        //         ->toArray();
        // } else {
            $products = Product::query()
                ->where('is_showed', '=', 1)
                ->whereIn('id', $product_id)
                ->orderBy('main_order', 'asc')
                ->get(['id', 'image', 'aktiv'])
                ->toArray();
        // }



        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = 'product/' . $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', str_replace("\r\n", '', str_replace(' ', '', $products[$i]['aktiv'])));
            $products[$i]['price'] = $product_price[$products[$i]['id']];
        }

        return $products;
    }

    public static function GetBonuses($pack_id = null)
    {
        $language_id = Language::$languages[App::currentLocale()];
        if (empty($pack_id)) {
            $bonus = DB::select("SELECT pack_id, pd.name, price, ptd.name as type
                            FROM bonus_packs bp
                            JOIN product_packaging pp ON pp.id = bp.pack_id
                            JOIN product_desc pd ON pd.product_id = pp.product_id
                            JOIN product_type_desc ptd on pp.type_id = ptd.type_id
                            WHERE pd.language_id = $language_id AND ptd.language_id = $language_id AND bp.is_showed = 1");
        }
        else
        {
            $bonus = DB::select("SELECT pack_id, pd.name, price, ptd.name as type
                            FROM bonus_packs bp
                            JOIN product_packaging pp ON pp.id = bp.pack_id
                            JOIN product_desc pd ON pd.product_id = pp.product_id
                            JOIN product_type_desc ptd on pp.type_id = ptd.type_id
                            WHERE pd.language_id = $language_id AND ptd.language_id = $language_id AND bp.is_showed = 1 AND bp.pack_id = $pack_id");
        }

        foreach ($bonus as &$product) {
            switch ($product->pack_id) {
                case 11630:
                    $desc = "Viagra 100 mg x 1 $product->type, Cialis 20 mg x 1 $product->type, Levitra 20 mg x 1 $product->type";
                    break;
                case 4576:
                    $desc = "Viagra 100 mg x 5 $product->type, Cialis 20 mg x 5 $product->type, Levitra 20 mg x 5 $product->type";
                    break;
                case 4577:
                    $desc = "Viagra 100 mg x 10 $product->type, Cialis 20 mg x 10 $product->type, Levitra 20 mg x 10 $product->type";
                    break;
                case 4919:
                    $desc = "Viagra 100 mg x 20 $product->type, Cialis 20 mg x 20 $product->type, Levitra 20 mg x 20 $product->type";
                    break;
                case 11656:
                    $desc = "Viagra 100 mg x 30 $product->type, Cialis 20 mg x 30 $product->type, Levitra 20 mg x 30 $product->type";
                    break;
                case 11655:
                    $desc = "Viagra 100 mg x 30 $product->type, Female Viagra 100 mg x 30 $product->type";
                    break;
                default:
                    $desc = "";
                    break;
            }

            $product->desc = $desc;
        }
        unset($product);

        $bonus_free = (object)[
            'pack_id' => 0,
            'name' => 'No Bonus',
            'price' => 0,
            'type' => 'none',
            'desc' => ''
        ];

        array_unshift($bonus, $bonus_free);

        if (!empty($pack_id))
            $bonus = $bonus[0];

        return $bonus;
    }

    public static function GetGiftCard()
    {
        $language_id = Language::$languages[App::currentLocale()];
        $cards = DB::select("SELECT pp.id as pack_id, pd.name, price
            FROM product_packaging pp
            JOIN product_desc pd ON pd.product_id = pp.product_id
            WHERE pd.language_id = $language_id
            AND pp.is_showed = 1
            AND dosage = '1card'
            ORDER BY price ASC");

        return $cards;
    }
}
