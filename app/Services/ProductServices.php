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
use Illuminate\Support\Str;

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

            $card = Product::query()
                ->where('id', '=', 616)
                ->where('is_showed_on_main', '=', 1)
                ->where('is_showed', '=', 1)
                ->get(['product.id', 'product.image', 'product.aktiv'])
                ->toArray();

            if ($card) {
                array_unshift($products, $card[0]);
            }
        } else {
            $products = Product::query()
                ->where('is_showed_on_main', '=', 1)
                ->where('is_showed', '=', 1)
                ->orderBy('main_order', 'asc')
                ->get(['id', 'image', 'aktiv'])
                ->toArray();
        }

        if (env('APP_GIFT_CARD') == 0) {
            foreach ($products as $key => $product) {
                if ($product['id'] == 616) {
                    unset($products[$key]);
                    break;
                }
            }
        }

        foreach ($products as $i => $product) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', str_replace("\r\n", '', ucwords(trim($products[$i]['aktiv']))));
            foreach ($products[$i]['aktiv'] as $key => $value) {
                $products[$i]['aktiv'][$key] = [
                    'name' => trim($value),
                    'url' => str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))))
                ];
            }

            foreach ($product_price as $key=>$pp) {
                if ($product['id'] == $key) {
                    $products[$i]['price'] = $product_price[$products[$i]['id']];
                }
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
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
                ->where('is_showed' , '=', 1)
                ->with(['product'])
                ->orderBy('ord')
                ->get();
            } else {
                $categories_raw = Category::query()
                ->with(['product'])
                ->where('is_showed' , '=', 1)
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
                    ->where('is_showed' , '=', 1)
                    ->with(['product'])
                    ->orderBy('ord')
                    ->get();
            } else {
                $categories_raw = Category::query()
                    ->where('url', '=', $url)
                    ->where('is_showed' , '=', 1)
                    ->with(['product'])
                    ->orderBy('ord')
                    ->get();
            }
        }

        $categories = [];
        foreach($categories_raw as $category)
        {
            $products = $category->product->where('is_showed', '=', 1)->sortBy('menu_order')->toArray();

            if (env('APP_GIFT_CARD') == 0) {
                foreach($products as $key => $product) {
                    if ($product['id'] == 616) {
                        unset($products[$key]);
                        break;
                    }
                }
            }

            foreach($products as &$product)
            {
                foreach ($product_price as $key=>$pp) {
                    if ($product['id'] == $key) {
                        $product['price'] = $product_price[$product['id']];
                    }
                }

                $product['name'] = $products_desc[$product['id']]['name'];
                $product['desc'] = $products_desc[$product['id']]['desc'];
                $product['url'] = $products_desc[$product['id']]['url'];
                $product['aktiv'] = explode(',', ucwords(trim(str_replace("\r\n", '', trim($product['aktiv'])))));

                foreach ($product['aktiv'] as $key => $value) {
                    $product['aktiv'][$key] = [
                        'name' => trim($value),
                        'url' => str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))))
                    ];
                }
            }

            foreach ($products as &$product) {
                if (!isset($product['price'])) {
                    $product['price'] = 0;
                }
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
        $products_desc_raw = [];
        // if (env('APP_GIFT_CARD')) {
            if(empty($url))
            {
                $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();
            }
            else
            {
                $products_desc_raw = ProductDesc::query()->join('product', 'product_desc.product_id', '=', 'product.id')->where('product.is_showed', '=', 1)->where('product_desc.language_id', '=', $language_id)->where('product_desc.url', '=', $url)->get(['product_desc.product_id', 'product_desc.name', 'product_desc.desc', 'product_desc.url'])->groupBy('product_desc.product_id')->toArray();

                if (!$products_desc_raw || empty($products_desc_raw)) {
                    $product_id = DB::select("SELECT p.id FROM product p INNER JOIN product_category pc ON pc.product_id = p.id INNER JOIN category ca ON ca.id = pc.category_id WHERE ca.is_showed = 1 AND p.sinonim LIKE '%{$url}%' AND p.is_showed = 1");

                    if(empty($product_id)) {
                        $url = str_replace('-', ' ', $url);
                        $product_id = DB::select("SELECT p.id FROM product p INNER JOIN product_category pc ON pc.product_id = p.id INNER JOIN category ca ON ca.id = pc.category_id WHERE ca.is_showed = 1 AND p.sinonim LIKE '%{$url}%' AND p.is_showed = 1");
                    }

                    if(!empty($product_id))
                    {
                        $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->where('product_id', '=', $product_id[0]->id)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();
                        foreach($products_desc_raw as $key => $product) {
                            $products_desc_raw[$key][0]['name'] = ucfirst($url) . ' (' . __('text.product_other_name') . $product[0]['name'] . ')';
                        }
                    }
                }
            }
        // } else {
        //     if(empty($url))
        //     {
        //         $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->where('product_id', '<>', 616)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();
        //     }
        //     else
        //     {
        //         $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->where('product_id', '<>', 616)->where('url', '=', $url)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();

        //         if (!$products_desc_raw || empty($products_desc_raw)) {
        //             $product_id = DB::select("SELECT p.id FROM product p INNER JOIN product_category pc ON pc.product_id = p.id INNER JOIN category ca ON ca.id = pc.category_id WHERE ca.is_showed = 1 AND p.`id` <> 616 AND p.sinonim LIKE '%{$url}%' AND p.is_showed = 1");
        //             $products_desc_raw = ProductDesc::query()->where('language_id', '=', $language_id)->where('product_id', '=', $product_id[0]->id)->get(['product_id', 'name', 'desc', 'url'])->groupBy('product_id')->toArray();

        //             foreach($products_desc_raw as $key => $product) {
        //                 $products_desc_raw[$key][0]['name'] = ucfirst($url) . ' (' . __('text.product_other_name') . $product[0]['name'] . ')';
        //             }
        //         }
        //     }
        // }


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

        $products = Product::query()
            ->where('is_showed', '=', 1)
            ->where('first_letter', '=', $letter)
            ->orderBy('main_order', 'asc')
            ->get(['id', 'image', 'aktiv'])
            ->toArray();

        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', ucwords(str_replace("\r\n", '', trim($products[$i]['aktiv']))));
            foreach ($products[$i]['aktiv'] as $key => $value) {
                $products[$i]['aktiv'][$key] = [
                    'name' => trim($value),
                    'url' => str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))))
                ];
            }
            foreach ($product_price as $key=>$pp) {
                if ($products[$i]['id'] == $key) {
                    $products[$i]['price'] = $product_price[$products[$i]['id']];
                }
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
        }

        return $products;
    }

    public static function GetProductByDisease($disease, $design)
    {
        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);
        $disease = str_replace('-', ' ', $disease);

        $diseases = DB::select('SELECT * FROM product_disease WHERE language_id = ? AND disease = ?', [Language::$languages[App::currentLocale()], $disease]);

        $product_id = [];
        foreach ($diseases as $item) {
            $product_id[] = $item->product_id;
        }

        $products = Product::query()
            ->where('is_showed', '=', 1)
            ->whereIn('id', $product_id)
            ->orderBy('main_order', 'asc')
            ->get(['id', 'image', 'aktiv'])
            ->toArray();

        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', ucwords(str_replace("\r\n", '', trim($products[$i]['aktiv']))));
            foreach ($products[$i]['aktiv'] as $key => $value) {
                $products[$i]['aktiv'][$key] = [
                    'name' => trim($value),
                    'url' => str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))))
                ];
            }
            foreach ($product_price as $key=>$pp) {
                if ($products[$i]['id'] == $key) {
                    $products[$i]['price'] = $product_price[$products[$i]['id']];
                }
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
        }

        return $products;
    }

    public static function GetProductByActive($active, $design)
    {
        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);

        $active = str_replace('-', ' ', $active);

        $products = Product::query()
            ->where('is_showed', '=', 1)
            ->where('aktiv', 'LIKE', "%$active%")
            ->orderBy('main_order', 'asc')
            ->get(['id', 'image', 'aktiv'])
            ->toArray();

        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', ucwords(str_replace("\r\n", '', trim($products[$i]['aktiv']))));
            foreach ($products[$i]['aktiv'] as $key => $value) {
                $products[$i]['aktiv'][$key] = [
                    'name' => trim($value),
                    'url' => str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))))
                ];
            }
            foreach ($product_price as $key=>$pp) {
                if ($products[$i]['id'] == $key) {
                    $products[$i]['price'] = $product_price[$products[$i]['id']];
                }
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
        }

        return $products;
    }

    public static function GetProductInfoByUrl($url, $design)
    {
        $language_id = Language::$languages[App::currentLocale()];
        $products_desc = self::GetProductDesc($language_id, $url);

        if(empty($products_desc))
        {
            return [];
        }

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
            'SELECT DISTINCT pd.name, pd.url
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
        $sinonims = str_replace("\u{FEFF}", '', $sinonims);
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
        $rec_id = 0;
        if ($products_desc['product_id'] === 285) {
            $rec_id = 286;
            // $rec_name = 'Viagra Extra Dosage';
            // $rec_url = 'viagra-extra-dosage';
        }
        if ($products_desc['product_id'] === 233) {
            $rec_id = 235;
            // $rec_name = 'Cialis Extra Dosage';
            // $rec_url = 'cialis-extra-dosage';
        }
        if ($products_desc['product_id'] === 255) {
            $rec_id = 256;
            // $rec_name = 'Levitra Extra Dosage';
            // $rec_url = 'levitra-extra-dosage';
        }
        if ($products_desc['product_id'] === 278) {
            $rec_id = 247;
            // $rec_name = 'Extra Super Viagra';
            // $rec_url = 'extra-super-viagra';
        }
        if ($products_desc['product_id'] === 274) {
            $rec_id = 245;
            // $rec_name = 'Extra Super Cialis';
            // $rec_url = 'extra-super-cialis';
        }
        if ($products_desc['product_id'] === 275) {
            $rec_id = 246;
            // $rec_name = 'Extra Super Levitra';
            // $rec_url = 'extra-super-levitra';
        }
        if ($products_desc['product_id'] === 273) {
            $rec_id = 244;
            // $rec_name = 'Extra Super Avana';
            // $rec_url = 'extra-super-avana';
        }

        if ($rec_id) {
            $rec_info = DB::select("SELECT `name`, `url` FROM `product_desc` WHERE `product_id` = $rec_id AND `language_id` = $language_id");
            $rec_info = $rec_info[0];
            $rec_name = $rec_info->name;
            $rec_url = $rec_info->url;
        }

        $product['categories'] = $categories;
        $product['name'] = $products_desc['name'];
        $product['desc'] = $products_desc['desc'];
        $product['aktiv'] = $product['aktiv'] ? explode(',', trim(ucwords($product['aktiv']))) : [];

        if (count($product['aktiv']) > 0) {
            foreach ($product['aktiv'] as $key => $value) {
                $product['aktiv'][$key] = [
                    'name' => trim($value),
                    'url' => str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))))
                ];
            }
        }

        $product['disease'] = $product_disease;
        $product['analog'] = json_decode(json_encode($analogs), true);
        $product['sinonim'] = $product['sinonim'];
        $path = public_path() . '/languages/' . App::currentLocale() . '/tablets_descriptions/' . $product['product_info_file_path'];
        $product['full_desc'] = File::exists($path) ? File::get($path) : '';
        $product['packs'] = $packs;
        $product['type'] = $type;
        $product['rec_name'] = $rec_name;
        $product['rec_url'] = $rec_url;

        $product_description = $product['full_desc'];
        $product_description = str_replace("#TOP_TAG#", '<div class="full_text" style="margin-top: 16px;">', $product_description);
        $product_description = str_replace("#TITLE_OPEN_TAG#", '<h3><img src="/images/elements/more_info.png" alt="" style="position: relative; top: 5px;"/>', $product_description);
        $product_description = str_replace("#TITLE_CLOSE_TAG#", '</h3>', $product_description);
        $product_description = str_replace("#BLOCK_OPEN_TAG#", '<p>', $product_description);
        $product_description = str_replace("#BLOCK_CLOSE_TAG#", '</p>', $product_description);
        $product_description = str_replace("#LIST_OPEN_TAG#", '<ul>', $product_description);
        $product_description = str_replace("#LIST_ELEMENT_OPEN_TAG#", '<li>', $product_description);
        $product_description = str_replace("#LIST_ELEMENT_CLOSE_TAG#", '</li>', $product_description);
        $product_description = str_replace("#LIST_CLOSE_TAG#", '</ul>', $product_description);
        $product_description = str_replace("#BOTTOM_TAG#", '</div>', $product_description);
        $product_description = str_replace("#NEXT_LINE_TAG#", '<br />', $product_description);

        $product['full_desc'] = $product_description;

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

    public static function SearchProductAutocomplete($search_text, $design)
    {
        $language_id = Language::$languages[App::currentLocale()];
        if ($design == "design_5")
        {
            $products = DB::table('product_desc')
            ->join('product_category', 'product_desc.product_id', '=', 'product_desc.product_id')
            ->join('product', 'product.id', '=', 'product_desc.product_id')
            ->distinct()
            ->where('product_desc.name', 'LIKE', '%' . $search_text . '%')
            ->where('product.is_showed', '=', '1')
            ->whereIn('product_category.category_id', [14, 21])
            ->orderBy('product.menu_order', 'asc')
            ->get(['product_desc.product_id', 'product_desc.name', 'product_desc.url', 'product.menu_order'])
            ->toArray();
        }
        else
        {
            $products = DB::table('product_desc')
            ->join('product_category', 'product_desc.product_id', '=', 'product_desc.product_id')
            ->join('product', 'product.id', '=', 'product_desc.product_id')
            ->distinct()
            ->where('product_desc.name', 'LIKE', '%' . $search_text . '%')
            ->where('product.is_showed', '=', '1')
            ->orderBy('product.menu_order', 'asc')
            ->get(['product_desc.product_id', 'product_desc.name', 'product_desc.url', 'product.menu_order'])
            ->toArray();
        }

        $tips = "";
        foreach($products as $product)
        {
            $tips .= $product->name . "||" . $product->url . ".html\n";
        }

        return $tips;

    }

    public static function SearchProduct($search_text, $is_autocomplete, $design)
    {
        if (str_contains($search_text, ' ')) {
            $search_text = '(' . $search_text . ')';
        }

        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);

        if ($design == 'design_5') {
            $product_ids = DB::table('product_search')
                ->join('product_category', 'product_search.product_id', '=', 'product_category.product_id')
                ->whereFullText('product_search.keyword', $search_text . '*', ['mode' => 'boolean'])
                ->whereIn('product_category.category_id', [14, 21])
                ->where('product_search.is_showed', '=', 1)
                ->get(['product_search.product_id', 'product_category.category_id'])
                ->groupBy('product_search.en_name')
                ->toArray();
        } else {
            if (env('APP_GIFT_CARD') == 0) {
                $product_ids = ProductSearch::whereFullText('keyword', $search_text . '*', ['mode' => 'boolean'])
                    ->distinct()
                    ->where('is_showed', '=', 1)
                    ->where('product_id', '<>', 616)
                    ->get(['product_id'])
                    ->toArray();
            } else {
                $product_ids = ProductSearch::whereFullText('keyword', $search_text . '*', ['mode' => 'boolean'])
                    ->distinct()
                    ->where('is_showed', '=', 1)
                    ->get(['product_id'])
                    ->toArray();
            }
        }

        $product_id = [];

        if ($design == 'design_5') {
            foreach ($product_ids as $product) {
                foreach ($product as $item) {
                    $product_id[] = $item->product_id;
                }
            }
        } else {
            foreach ($product_ids as $item) {
                $product_id[] = $item['product_id'];
            }
        }

        if (env('APP_GIFT_CARD') == 0) {
            foreach ($product_id as $key => $val) {
                if ($val == 616) {
                    unset($product_id[$key]);
                }
            }
        }

        $products = Product::query()
            ->where('is_showed', '=', 1)
            ->whereIn('id', $product_id)
            ->orderBy('main_order', 'asc')
            ->get(['id', 'image', 'aktiv'])
            ->toArray();

        for ($i = 0; $i < count($products); $i++) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];
            $products[$i]['url'] = $is_autocomplete ? $products_desc[$products[$i]['id']]['url'] . '.html' : $products_desc[$products[$i]['id']]['url'];
            $products[$i]['aktiv'] = explode(',', ucwords(str_replace("\r\n", '', trim($products[$i]['aktiv']))));
            foreach ($products[$i]['aktiv'] as $key => $value) {
                $products[$i]['aktiv'][$key] = [
                    'name' => trim($value),
                    'url' => str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))))
                ];
            }
            foreach ($product_price as $key=>$pp) {
                if ($products[$i]['id'] == $key) {
                    $products[$i]['price'] = $product_price[$products[$i]['id']];
                }
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
        }

        return $products;
    }

    public static function SearchPageTitle($search_text)
    {
        $information = trans('text.information');
        $array = [];
        $array[] = trans('text.common_about_us_main_menu_item') . " ($information)" . '||' . 'about';
        $array[] = trans('text.common_help_main_menu_item') . " ($information)" . '||' . 'help';
        $array[] = trans('text.common_testimonials_main_menu_item') . " ($information)" . '||' . 'testimonials';
        $array[] = trans('text.common_shipping_main_menu_item') . " ($information)" . '||' . 'delivery';
        $array[] = trans('text.common_moneyback_main_menu_item') . " ($information)" . '||' .  'moneyback';
        $array[] = trans('text.common_contact_us_main_menu_item') . " ($information)" . '||' . 'contact_us';

        $collection = collect($array);

        $filtered = $collection->filter(function($item) use ($search_text) {
            return stripos($item,$search_text) !== false;
        });

        $result = $filtered->first();

        return $result;
    }

    public static function SearchCategory($search_text)
    {
        $category = trans('text.common_category_search');
        $language_id = Language::$languages[App::currentLocale()];
        $result = DB::select("SELECT cd.name, c.url FROM category c JOIN category_desc cd ON c.id = cd.category_id WHERE c.is_showed = 1 AND language_id = ? AND cd.name LIKE ?", [$language_id, '%' . $search_text . '%']);

        $tips = "";
        foreach($result as $item)
        {
            $tips .= $item->name . " $category||category/" . $item->url . "\n";
        }

        return $tips;
    }

    public static function SearchDisease($search_text)
    {
        $disease = trans('text.common_disease_search');
        $language_id = Language::$languages[App::currentLocale()];
        // $result = ProductDisease::where("disease", "LIKE", "%$search_text%")->where('language_id', '=', $language_id)->distinct()->get('disease')->toArray();
        $result = DB::select("SELECT pd.disease FROM product p
                            JOIN product_disease pd ON pd.product_id = p.id
                            WHERE pd.disease LIKE ? AND pd.language_id = ? AND p.is_showed = 1",
                            ['%' . $search_text . '%', $language_id]);

        $tips = "";
        foreach($result as $item)
        {
            $tips .= $item->disease . " $disease||disease/" .  str_replace(' ', '-', strtolower($item->disease)) . "\n";
        }

        return $tips;
    }

    public static function SearchActive($search_text)
    {
        $aktiv = trans('text.common_aktiv_search');
        $all_active = Product::distinct()->where('is_showed', '=', 1)->get('aktiv')->toArray();

        $active = [];
        foreach($all_active as $item)
        {
            $a = explode(',', $item['aktiv']);
            foreach($a as $ak)
            {
                $active[] = ucfirst(trim($ak));
            }
        }

        $active = array_values(array_unique($active));

        $active = collect($active);

        $result = $active->filter(function($item) use ($search_text) {
            return stripos($item, $search_text) !== false;
        });

        $tips = "";
        foreach($result->toArray() as $item)
        {
            if(trim($item) != '')
            {
                $url = str_replace(' ', '-', (Str::lower($item)));
                $tips .= trim($item) . " $aktiv||active/" . $url . "\n";
            }
        }

        return $tips;
    }

    public static function SearchSinonim($search_text)
    {
        try
        {
            $product = Product::query()
            ->where('sinonim', 'LIKE', "%$search_text%")
            ->where('sinonim', '!=', '')
            ->where('is_showed', '=', 1)
            ->get()->toArray();

            $tips = "";
            $result = [];
            foreach($product as $item)
            {
                $sinonims = explode("\n", $item['sinonim']);
                foreach($sinonims as $s)
                {
                    if(stripos($s, $search_text) !== false)
                    {
                        $s = trim($s);
                        $result[] = preg_replace('/[^A-Za-z0-9\s\-]/', '', $s);
                    }
                }
            }

            $result = array_unique($result);

            foreach($result as $item)
            {
                $tips .= $item . "||" . Str::lower(str_replace(' ', '-', $item)) . ".html\n";
            }

        }
        catch(\Exception $e)
        {
            $tips = "";
        }

        return $tips;
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

    public static function getPageProperties($page) {
        $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if ($domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $language_id = Language::$languages[App::currentLocale()];

        $page_properties = DB::select("SELECT * FROM page_properties WHERE `page` = '$page' AND `language` = $language_id");
        $page_properties = $page_properties[0];

        $page_properties->title = str_replace('(host_name)', $domain, $page_properties->title);
        $page_properties->keyword = str_replace('(host_name)', $domain, $page_properties->keyword);
        $page_properties->description = str_replace('(host_name)', $domain, $page_properties->description);

        switch($page){
            case 'main':
                $main_titles = [
                    'Cheap Online Pharmacy',
                    'Cheap Medicines Online',
                    'Pharmacy Discount & Special Offers',
                    'Discount Online Pharmacy',
                    'Only Today: Big Discounts',
                    'Low Prices Today Only'
                ];

                $main_title_cache = DB::select("SELECT title FROM main_cache");

                if (count($main_title_cache) > 0) {
                    $title = $main_title_cache[0]->title;
                } else {
                    $new_title_key = array_rand($main_titles, 1);
                    $title = $main_titles[$new_title_key];
                    DB::insert("INSERT INTO `main_cache` (`title`) VALUES ('{$title}')");
                }

                $page_properties->title = str_replace('(random_text)', $title, $page_properties->title);

                $title = $page_properties->title;

                break;
            case 'login':
                $page_properties->title = __('text.login_title') . ' - ' . $domain;

                break;
            case 'cart':

                $total = session('total.all_in_currency') ? session('total.all_in_currency') : 0;
                $page_properties->keyword = str_replace('(cart_total)', $total, $page_properties->keyword);
                $page_properties->description = str_replace('(cart_total)', $total, $page_properties->description);

                break;
            case 'category':

                $category_name = session('category_name') ? session('category_name') : __('text.category_title');
                $page_properties->title = str_replace('(category_name)', $category_name, $page_properties->title);
                $page_properties->keyword = str_replace('(category_name)', $category_name, $page_properties->keyword);
                $page_properties->description = str_replace('(category_name)', $category_name, $page_properties->description);

                break;
        }

        return $page_properties;
    }

    public static function getProductProperties($product) {
        $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;
        if ($domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $language_id = Language::$languages[App::currentLocale()];

        $page_properties = DB::select("SELECT * FROM page_properties WHERE `page` = 'product' AND `language` = $language_id");
        $page_properties = $page_properties[0];

        $page_properties->title = str_replace('(host_name)', $domain, $page_properties->title);
        $page_properties->keyword = str_replace('(host_name)', $domain, $page_properties->keyword);
        $page_properties->description = str_replace('(host_name)', $domain, $page_properties->description);

        $product_name = ucwords(str_replace('-', ' ', $product));
        $page_properties->title = str_replace('(product_name)', $product_name, $page_properties->title);
        $page_properties->keyword = str_replace('(product_name)', $product_name, $page_properties->keyword);
        $page_properties->description = str_replace('(product_name)', $product_name, $page_properties->description);

        $product_properties_new = DB::select("SELECT `title`, `keywords`, `description` FROM product_desc WHERE `url` = '$product' AND `language_id` = $language_id");

        if (count($product_properties_new) > 0) {
            $product_properties_new = $product_properties_new[0];

            if ($product_properties_new->title != '') {
                $page_properties->title = $product_properties_new->title;
            }

            if ($product_properties_new->keywords != '') {
                $page_properties->keyword = $product_properties_new->keywords;
            }

            if ($product_properties_new->description != '') {
                $page_properties->description = $product_properties_new->description;
            }
        } else {
            $product_id = DB::select("SELECT id FROM product WHERE sinonim LIKE '%$product%' AND is_showed = 1");

            if (empty($product_id)) {
                $product = str_replace('-', ' ', $product);
                $product_id = DB::select("SELECT id FROM product WHERE sinonim LIKE '%$product%' AND is_showed = 1");
            }

            if (!empty($product_id)) {
                $product_id = $product_id[0]->id;
                $product_properties_new = DB::select("SELECT `title`, `keywords`, `description` FROM product_desc WHERE `product_id` = $product_id AND `language_id` = $language_id");
                $product_properties_new = $product_properties_new[0];

                if ($product_properties_new->title != '') {
                    $page_properties->title = $product_properties_new->title;
                }

                if ($product_properties_new->keywords != '') {
                    $page_properties->keyword = $product_properties_new->keywords;
                }

                if ($product_properties_new->description != '') {
                    $page_properties->description = $product_properties_new->description;
                }
            } else {
                $page_properties->title = str_replace('(product_name)', $product_name, $page_properties->title);
                $page_properties->keyword = str_replace('(product_name)', $product_name, $page_properties->keyword);
                $page_properties->description = str_replace('(product_name)', $product_name, $page_properties->description);
            }
        }

        return $page_properties;
    }

    public static function getFirstLetters() {
        $design = session('design') ? session('design') : config('app.design');
        if ($design == 'design_5') {
            $first_letters_result = DB::table('product')
                ->distinct()
                ->join('product_category', 'product.id', '=', 'product_category.product_id')
                ->join('category', 'product_category.category_id', '=', 'category.id')
                ->where('product.is_showed', '=', 1)
                ->where('category.is_showed', '=', 1)
                ->where('category.id', '=', 14)
                ->get(['product.first_letter'])
                ->toArray();

        } else {
            $first_letters_result = DB::table('product')
                ->distinct()
                ->join('product_category', 'product.id', '=', 'product_category.product_id')
                ->join('category', 'product_category.category_id', '=', 'category.id')
                ->where('product.is_showed', '=', 1)
                ->where('category.is_showed', '=', 1)
                ->get(['product.first_letter'])
                ->toArray();
        }

        $distinct_first_letters = [];
        foreach ($first_letters_result as $cur_first_letter) {
            $distinct_first_letters[] = $cur_first_letter->first_letter;
        }

        $first_letters = [];
        for($index = ord('A'); $index <= ord('Z'); $index++) {
            if(in_array(chr($index), $distinct_first_letters)) {
                $first_letters[chr($index)] = true;
            } else {
                $first_letters[chr($index)] = false;
            }
        }

        return $first_letters;
    }

    public static function getDevice($agent) {
        $device = 'desktop';
        if ($agent->is('iPhone')) {
            $device = 'apple';
        }
        if ($agent->is('iPad')) {
            $device = 'apple';
        }
        if ($agent->is('iPod')) {
            $device = 'apple';
        }
        if ($agent->isAndroidOS()) {
            $device = 'android';
        }
        if ($agent->isDesktop()) {
            $device = 'desktop';
        }

        return $device;
    }
}