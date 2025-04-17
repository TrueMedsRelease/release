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
use Illuminate\Support\Facades\Http;

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

        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());

        foreach ($products as $i => $product) {
            $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
            $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];

            if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $products[$i]['url'] = 'Buying_'.$products_desc[$products[$i]['id']]['url'].'_online';
                } else {
                    $products[$i]['url'] = __('text.text_aff_domain_1') . '_' . $products_desc[$products[$i]['id']]['url'] . '_' . __('text.text_aff_domain_2');
                }
            }
            else {
                $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
            }

            $products[$i]['aktiv'] = explode(',', str_replace("\r\n", '', ucwords(trim($products[$i]['aktiv']))));
            foreach ($products[$i]['aktiv'] as $key => $value) {

                $aktive_url = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))));

                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $aktive_url = 'Buying_' . $aktive_url . "_online";
                } else {
                    $aktive_url = __('text.text_aff_domain_1') . '_' . $aktive_url . "_" . __('text.text_aff_domain_2');
                }

                $products[$i]['aktiv'][$key] = [
                    'name' => trim($value),
                    'url' => $aktive_url
                ];
            }

            foreach ($product_price as $key=>$pp) {
                if ($product['id'] == $key) {
                    $products[$i]['price'] = $product_price[$products[$i]['id']]['price'];

                    if (isset($product_price[$products[$i]['id']]['discount'])) {
                        $products[$i]['discount'] = $product_price[$products[$i]['id']]['discount'];
                    }
                }
            }

            $products[$i]['alt'] = $products[$i]['image'];

            if ($products[$i]['image'] != 'gift-card') {
                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    $products[$i]['image'] = $domainWithoutZone.'_'.$products[$i]['image'];
                    $products[$i]['alt'] = __('text.text_aff_domain_1') . '_' . $products[$i]['name'] . '_' . __('text.text_aff_domain_2');
                }
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
            if (!isset($product['discount'])) {
                $products[$i]['discount'] = 0;
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

        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());

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
                        $product['price'] = $product_price[$product['id']]['price'];

                        if (isset($product_price[$product['id']]['discount'])) {
                            $product['discount'] = $product_price[$product['id']]['discount'];
                        }
                    }
                }

                if (isset($products_desc[$product['id']])) {
                    $product['name'] = $products_desc[$product['id']]['name'];
                    $product['desc'] = $products_desc[$product['id']]['desc'];

                    if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                        if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                            $product['url'] = 'Buying_'.$products_desc[$product['id']]['url'].'_online';
                        } else {
                            $product['url'] = __('text.text_aff_domain_1') . '_' . $products_desc[$product['id']]['url'] . '_' . __('text.text_aff_domain_2');
                        }
                    } else {
                        $product['url'] = $products_desc[$product['id']]['url'];
                    }

                    $product['aktiv'] = explode(',', ucwords(trim(str_replace("\r\n", '', trim($product['aktiv'])))));

                    $product['alt'] = $product['image'];

                    if ($product['image'] != 'gift-card') {
                        if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                            $product['image'] = $domainWithoutZone.'_'.$product['image'];
                            $product['alt'] = __('text.text_aff_domain_1') . '_' . $product['name'] . '_' . __('text.text_aff_domain_2');
                        }
                    }

                    foreach ($product['aktiv'] as $key => $value) {

                        $aktive_url = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))));

                        if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                            $aktive_url = 'Buying_' . $aktive_url . "_online";
                        } else {
                            $aktive_url = __('text.text_aff_domain_1') . '_' . $aktive_url . "_" . __('text.text_aff_domain_2');
                        }

                        $product['aktiv'][$key] = [
                            'name' => trim($value),
                            'url' => $aktive_url
                        ];
                    }
                } else {
                    $product['unset'] = true;
                }
            }

            foreach ($products as &$product) {
                if (!isset($product['price'])) {
                    $product['price'] = 0;
                }
                if (!isset($product['discount'])) {
                    $product['discount'] = 0;
                }
            }

            unset($product);

            foreach($products as $key=>$val) {
                if (isset($val['unset']) && $val['unset'] == 'true'){
                    unset($products[$key]);
                }
            }

            if (isset($category_desc[$category->id])) {
                $category_url = $category->url;

                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $category_url = 'Buying_' . $category_url . "_online";
                } else {
                    $category_url = __('text.text_aff_domain_1') . '_' . $category_url . "_" . __('text.text_aff_domain_2');
                }
                $categories[$category->id]['url'] = $category_url;
                $categories[$category->id]['name'] = $category_desc[$category->id];
                $categories[$category->id]['products'] = $products;
            }
        }

        foreach ($categories as $key => $category) {
            if (empty($category['products'])) {
                unset($categories[$key]);
            }
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
                    // $product_id = DB::select("SELECT p.id FROM product p INNER JOIN product_category pc ON pc.product_id = p.id INNER JOIN category ca ON ca.id = pc.category_id WHERE ca.is_showed = 1 AND p.sinonim LIKE '%{$url}%' AND p.is_showed = 1");
                    $product_id = DB::table('product')
                        ->join('product_category', 'product_category.product_id', '=', 'product.id')
                        ->join('category', 'category.id', '=', 'product_category.category_id')
                        ->where('category.is_showed', '=', 1)
                        ->where('product.sinonim', 'like', '%' . $url . '%')
                        ->where('product.is_showed', '=', 1)
                        ->get(['product.id'])
                        ->toArray();

                    if(empty($product_id)) {
                        $url = str_replace('-', ' ', $url);
                        // $product_id = DB::select("SELECT p.id FROM product p INNER JOIN product_category pc ON pc.product_id = p.id INNER JOIN category ca ON ca.id = pc.category_id WHERE ca.is_showed = 1 AND p.sinonim LIKE '%{$url}%' AND p.is_showed = 1");

                        $product_id = DB::table('product')
                            ->join('product_category', 'product_category.product_id', '=', 'product.id')
                            ->join('category', 'category.id', '=', 'product_category.category_id')
                            ->where('category.is_showed', '=', 1)
                            ->where('product.sinonim', 'like', '%' . $url . '%')
                            ->where('product.is_showed', '=', 1)
                            ->get(['product.id'])
                            ->toArray();

                        if(empty($product_id)) {
                            $url = str_replace(',', '.', $url);
                            // $product_id = DB::select("SELECT p.id FROM product p INNER JOIN product_category pc ON pc.product_id = p.id INNER JOIN category ca ON ca.id = pc.category_id WHERE ca.is_showed = 1 AND p.sinonim LIKE '%{$url}%' AND p.is_showed = 1");

                            $product_id = DB::table('product')
                                ->join('product_category', 'product_category.product_id', '=', 'product.id')
                                ->join('category', 'category.id', '=', 'product_category.category_id')
                                ->where('category.is_showed', '=', 1)
                                ->where('product.sinonim', 'like', '%' . $url . '%')
                                ->where('product.is_showed', '=', 1)
                                ->get(['product.id'])
                                ->toArray();
                        }
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
        $product = DB::select('SELECT product_id, MIN(`price` / `num`) as min FROM product_packaging WHERE is_showed = 1 AND price != 0 GROUP BY product_id');
        $product_info = DB::select('SELECT pp.product_id, pd.dosage, MAX(pp.num) AS max_num, MIN(pp.num) AS min_num, MIN(pp.price) as min_price, MAX(pp.price) as max_price
            FROM product_dosage pd
            JOIN product_packaging pp ON pd.product_id = pp.product_id AND pd.dosage = pp.dosage
            WHERE pp.is_showed = 1 AND pp.price != 0
            GROUP BY pp.product_id, pd.dosage
            ORDER BY pp.product_id;'
        );

        $product_price = [];
        foreach ($product as $p) {
            $product_price[$p->product_id] = [
                'price' => round($p->min, 2)
            ];
        }

        $product_info_arr = [];
        foreach ($product_info as $key => $info) {
            $product_info_arr[] = [
                'product_id' => $info->product_id,
                'dosage' => $info->dosage,
                'discount' => ceil(100 - ($info->max_price / (($info->min_price / $info->min_num) * $info->max_num)) * 100)
            ];
        }

        foreach ($product_price as $id => $price) {
            foreach ($product_info_arr as $key => $info) {
                if ($info['product_id'] == $id) {
                    $discount = $info['discount'];

                    if (isset($product_price[$id]['discount']) && $product_price[$id]['discount'] > $discount) {
                        $discount_new = $product_price[$id]['discount'];
                    } else {
                        $discount_new = $discount;
                    }

                    $product_price[$id] = [
                        'price'=> $price['price'],
                        'discount' => $discount_new
                    ];
                }
            }
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

        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());

        for ($i = 0; $i < count($products); $i++) {
            if (isset($products_desc[$products[$i]['id']])) {
                $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
                $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];

                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $products[$i]['url'] = 'Buying_'.$products_desc[$products[$i]['id']]['url'].'_online';
                    } else {
                        $products[$i]['url'] = __('text.text_aff_domain_1') . '_' . $products_desc[$products[$i]['id']]['url'] . '_' . __('text.text_aff_domain_2');
                    }
                } else {
                    $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
                }

                $products[$i]['alt'] = $products[$i]['image'];

                if ($products[$i]['image'] != 'gift-card') {
                    if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                        $products[$i]['image'] = $domainWithoutZone . '_' . $products[$i]['image'];
                        $products[$i]['alt'] = __('text.text_aff_domain_1') . '_' . $products[$i]['name'] . '_' . __('text.text_aff_domain_2');
                    }
                }

                $products[$i]['aktiv'] = explode(',', ucwords(str_replace("\r\n", '', trim($products[$i]['aktiv']))));
                foreach ($products[$i]['aktiv'] as $key => $value) {

                    $aktive_url = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))));

                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $aktive_url = 'Buying_' . $aktive_url . "_online";
                    } else {
                        $aktive_url = __('text.text_aff_domain_1') . '_' . $aktive_url . "_" . __('text.text_aff_domain_2');
                    }

                    $products[$i]['aktiv'][$key] = [
                        'name' => trim($value),
                        'url' => $aktive_url
                    ];
                }
                foreach ($product_price as $key=>$pp) {
                    if ($products[$i]['id'] == $key) {
                        $products[$i]['price'] = $product_price[$products[$i]['id']]['price'];

                        if (isset($product_price[$products[$i]['id']]['discount'])) {
                            $products[$i]['discount'] = $product_price[$products[$i]['id']]['discount'];
                        }
                    }
                }
            } else {
                $products[$i]['unset'] = true;
            }
        }

        foreach ($products as $key => $val) {
            if (isset($val['unset']) && $val['unset'] == 'true'){
                unset($products[$key]);
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
            if (!isset($product['discount'])) {
                $products[$i]['discount'] = 0;
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

        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());

        for ($i = 0; $i < count($products); $i++) {
            if (isset($products_desc[$products[$i]['id']])) {
                $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
                $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];

                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $products[$i]['url'] = 'Buying_'.$products_desc[$products[$i]['id']]['url'].'_online';
                    } else {
                        $products[$i]['url'] = __('text.text_aff_domain_1') . '_' . $products_desc[$products[$i]['id']]['url'] . '_' . __('text.text_aff_domain_2');
                    }
                } else {
                    $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
                }

                $products[$i]['alt'] = $products[$i]['image'];

                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    $products[$i]['image'] = $domainWithoutZone . '_' . $products[$i]['image'];
                    $products[$i]['alt'] = __('text.text_aff_domain_1') . '_' . $products[$i]['name'] . '_' . __('text.text_aff_domain_2');
                }

                $products[$i]['aktiv'] = explode(',', ucwords(str_replace("\r\n", '', trim($products[$i]['aktiv']))));
                foreach ($products[$i]['aktiv'] as $key => $value) {

                    $aktive_url = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))));

                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $aktive_url = 'Buying_' . $aktive_url . "_online";
                    } else {
                        $aktive_url = __('text.text_aff_domain_1') . '_' . $aktive_url . "_" . __('text.text_aff_domain_2');
                    }

                    $products[$i]['aktiv'][$key] = [
                        'name' => trim($value),
                        'url' => $aktive_url
                    ];
                }
                foreach ($product_price as $key=>$pp) {
                    if ($products[$i]['id'] == $key) {
                        $products[$i]['price'] = $product_price[$products[$i]['id']]['price'];

                        if (isset($product_price[$products[$i]['id']]['discount'])) {
                            $products[$i]['discount'] = $product_price[$products[$i]['id']]['discount'];
                        }
                    }
                }
            } else {
                $products[$i]['unset'] = true;
            }
        }

        foreach ($products as $key => $val) {
            if (isset($val['unset']) && $val['unset'] == 'true'){
                unset($products[$key]);
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
            if (!isset($product['discount'])) {
                $products[$i]['discount'] = 0;
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

        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());

        for ($i = 0; $i < count($products); $i++) {
            if (isset($products_desc[$products[$i]['id']])) {
                $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
                $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];

                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $products[$i]['url'] = 'Buying_'.$products_desc[$products[$i]['id']]['url'].'_online';
                    } else {
                        $products[$i]['url'] = __('text.text_aff_domain_1') . '_' . $products_desc[$products[$i]['id']]['url'] . '_' . __('text.text_aff_domain_2');
                    }
                } else {
                    $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
                }

                $products[$i]['alt'] = $products[$i]['image'];

                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    $products[$i]['image'] = $domainWithoutZone . '_' . $products[$i]['image'];
                    $products[$i]['alt'] = __('text.text_aff_domain_1') . '_' . $products[$i]['name'] . '_' . __('text.text_aff_domain_2');
                }

                $products[$i]['aktiv'] = explode(',', ucwords(str_replace("\r\n", '', trim($products[$i]['aktiv']))));
                foreach ($products[$i]['aktiv'] as $key => $value) {

                    $aktive_url = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))));

                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $aktive_url = 'Buying_' . $aktive_url . "_online";
                    } else {
                        $aktive_url = __('text.text_aff_domain_1') . '_' . $aktive_url . "_" . __('text.text_aff_domain_2');
                    }

                    $products[$i]['aktiv'][$key] = [
                        'name' => trim($value),
                        'url' => $aktive_url
                    ];
                }
                foreach ($product_price as $key=>$pp) {
                    if ($products[$i]['id'] == $key) {
                        $products[$i]['price'] = $product_price[$products[$i]['id']]['price'];

                        if (isset($product_price[$products[$i]['id']]['discount'])) {
                            $products[$i]['discount'] = $product_price[$products[$i]['id']]['discount'];
                        }
                    }
                }
            } else {
                $products[$i]['unset'] = true;
            }
        }

        foreach ($products as $key => $val) {
            if (isset($val['unset']) && $val['unset'] == 'true'){
                unset($products[$key]);
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
            if (!isset($product['discount'])) {
                $products[$i]['discount'] = 0;
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
                if ($n['name']) {
                    $name = $n['name'];
                }
            }

            if (isset($name)) {

                $category_url = $category->url;

                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $category_url = 'Buying_'.$category_url.'_online';
                } else {
                    $category_url = __('text.text_aff_domain_1') . '_' . $category_url . '_' . __('text.text_aff_domain_2');
                }

                $categories[] = ['name' => $name, 'url' => $category_url];
            }
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

            $disease_url = str_replace(' ', '-', $disease['disease']);

            if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                $disease_url = 'Buying_'.$disease_url.'_online';
            } else {
                $disease_url = __('text.text_aff_domain_1') . '_' . $disease_url . '_' . __('text.text_aff_domain_2');
            }

            $product_disease[] = [
                'name' => $disease['disease'],
                'url' => $disease_url
            ];
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

                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $new_sinonim_url = 'Buying_'.$sinonim_url.'_online';
                    } else {
                        $new_sinonim_url = __('text.text_aff_domain_1') . '_' . $sinonim_url . '_' . __('text.text_aff_domain_2');
                    }
                } else {
                    $new_sinonim_url = $sinonim_url;
                }

                $sinonims_new[] = array (
                    "name" => $sinonim_name,
                    "url" => $new_sinonim_url,
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

            if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $rec_url = 'Buying_'.$rec_info->url.'_online';
                } else {
                    $rec_url = __('text.text_aff_domain_1') . '_' . $rec_info->url . '_' . __('text.text_aff_domain_2');
                }
            } else {
                $rec_url = $rec_info->url;
            }
        }

        $product['categories'] = $categories;
        $product['name'] = $products_desc['name'];
        $product['desc'] = $products_desc['desc'];
        $product['aktiv'] = $product['aktiv'] ? explode(',', trim(ucwords($product['aktiv']))) : [];

        if (count($product['aktiv']) > 0) {
            foreach ($product['aktiv'] as $key => $value) {

                $aktive_url = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))));

                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $aktive_url = 'Buying_' . $aktive_url . "_online";
                } else {
                    $aktive_url = __('text.text_aff_domain_1') . '_' . $aktive_url . "_" . __('text.text_aff_domain_2');
                }

                $product['aktiv'][$key] = [
                    'name' => trim($value),
                    'url' => $aktive_url
                ];
            }
        }

        $product['disease'] = $product_disease;
        $product['analog'] = json_decode(json_encode($analogs), true);

        foreach ($product['analog'] as $i => $analog) {
            if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $product['analog'][$i]['url'] = 'Buying_'.$product['analog'][$i]['url'].'_online';
                } else {
                    $product['analog'][$i]['url'] = __('text.text_aff_domain_1') . '_' . $product['analog'][$i]['url'] . '_' . __('text.text_aff_domain_2');
                }
            }
        }

        // $path = public_path() . '/languages/' . App::currentLocale() . '/tablets_descriptions/' . $product['product_info_file_path'];
        $path = public_path() . '/language_codes/' . App::currentLocale() . '/' . $product['image'] . '.html';
        $path_en = public_path() . '/language_codes/en/' . $product['image'] . '.html';
        if (File::exists($path)) {
            $product['full_desc'] = File::get($path);
        } else if (File::exists($path_en)) {
            $product['full_desc'] = File::get($path_en);
        } else {
            $product['full_desc'] = '';
        }

        $product['alt'] = $product['image'];

        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
        if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
            $product['image'] = $domainWithoutZone.'_'.$product['image'];
            $product['alt'] = __('text.text_aff_domain_1').'_'.$product['name'].'_'.__('text.text_aff_domain_2');
        }

        $product['packs'] = $packs;
        $product['type'] = $type;
        $product['rec_name'] = $rec_name;
        $product['rec_url'] = $rec_url;

        $product_description = $product['full_desc'];
        $product_description = str_replace("#TOP_TAG#", '<div class="full_text" style="margin-top: 16px;">', $product_description);
        $product_description = str_replace("#TITLE_OPEN_TAG#", '<h3>', $product_description);
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
            if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $tips .= $product->name . "||Buying_" . $product->url . "_online.html\n";
                } else {
                    $tips .= $product->name . "||" . __('text.text_aff_domain_1') . '_' . $product->url . '_' . __('text.text_aff_domain_2') . ".html\n";
                }
            } else {
                $tips .= $product->name . "||" . $product->url . ".html\n";
            }
        }

        return $tips;

    }

    public static function SearchProduct($search_text, $is_autocomplete, $design)
    {
        if (Str::contains($search_text, ' ')) {
            $search_full_text = '"' . $search_text . '"';
        } else {
            $search_full_text = $search_text . '*';
        }

        $search_text_lower = strtolower($search_text);
        $search_full_text_lower = strtolower($search_full_text);

        $products_desc = self::GetProductDesc(Language::$languages[App::currentLocale()]);
        $product_price = self::GetAllProductPillPrice($design);

        if ($design == 'design_5') {
            if (env('APP_GIFT_CARD') == 0) {
                $exactMatchProductIds = DB::table('product_search')
                    ->join('product_category', 'product_search.product_id', '=', 'product_category.product_id')
                    ->whereRaw('LOWER(product_search.keyword) = ?', [$search_text_lower])
                    ->whereIn('product_category.category_id', [14, 21])
                    ->where('product_search.is_showed', '=', 1)
                    ->where('product_search.product_id', '<>', 616)
                    ->distinct()
                    ->pluck('product_search.product_id')
                    ->toArray();

                $partialMatchProductIds = DB::table('product_search')
                    ->join('product_category', 'product_search.product_id', '=', 'product_category.product_id')
                    ->whereFullText('product_search.keyword', $search_full_text_lower, ['mode' => 'boolean'])
                    ->whereIn('product_category.category_id', [14, 21])
                    ->whereNotIn('product_search.product_id', $exactMatchProductIds)
                    ->where('product_search.is_showed', '=', 1)
                    ->where('product_search.product_id', '<>', 616)
                    ->distinct()
                    ->pluck('product_search.product_id')
                    ->toArray();

                $product_ids = array_merge($exactMatchProductIds, $partialMatchProductIds);
            } else {
                $exactMatchProductIds = DB::table('product_search')
                    ->join('product_category', 'product_search.product_id', '=', 'product_category.product_id')
                    ->whereRaw('LOWER(product_search.keyword) = ?', [$search_text_lower])
                    ->whereIn('product_category.category_id', [14, 21])
                    ->where('product_search.is_showed', '=', 1)
                    ->distinct()
                    ->pluck('product_search.product_id')
                    ->toArray();

                $partialMatchProductIds = DB::table('product_search')
                    ->join('product_category', 'product_search.product_id', '=', 'product_category.product_id')
                    ->whereFullText('product_search.keyword', $search_full_text_lower, ['mode' => 'boolean'])
                    ->whereIn('product_category.category_id', [14, 21])
                    ->whereNotIn('product_search.product_id', $exactMatchProductIds)
                    ->where('product_search.is_showed', '=', 1)
                    ->distinct()
                    ->pluck('product_search.product_id')
                    ->toArray();

                $product_ids = array_merge($exactMatchProductIds, $partialMatchProductIds);
            }


            // $product_ids = DB::table('product_search')
            //     ->join('product_category', 'product_search.product_id', '=', 'product_category.product_id')
            //     ->where('product_search.keyword', 'like', '%' . $search_text . '%')
            //     ->whereIn('product_category.category_id', [14, 21])
            //     ->where('product_search.is_showed', '=', 1)
            //     ->get(['product_search.product_id', 'product_category.category_id'])
            //     ->groupBy('product_search.en_name')
            //     ->toArray();
        } else {
            if (env('APP_GIFT_CARD') == 0) {
                $exactMatchProductIds = ProductSearch::whereRaw('LOWER(keyword) = ?', [$search_text_lower])
                    ->where('is_showed', '=', 1)
                    ->where('product_id', '<>', 616)
                    ->distinct()
                    ->pluck('product_id')
                    ->toArray();

                $partialMatchProductIds = ProductSearch::whereFullText('keyword', $search_full_text_lower, ['mode' => 'boolean'])
                    ->where('is_showed', '=', 1)
                    ->where('product_id', '<>', 616)
                    ->whereNotIn('product_id', $exactMatchProductIds) // Исключаем уже найденные точные совпадения
                    ->distinct()
                    ->pluck('product_id')
                    ->toArray();

                $product_ids = array_merge($exactMatchProductIds, $partialMatchProductIds);

                // $product_ids = ProductSearch::where('keyword', 'like', '%' . $search_text . '%')
                //     ->distinct()
                //     ->where('is_showed', '=', 1)
                //     ->where('product_id', '<>', 616)
                //     ->get(['product_id'])
                //     ->toArray();
            } else {
                $exactMatchProductIds = ProductSearch::whereRaw('LOWER(keyword) = ?', [$search_text_lower])
                    ->where('is_showed', '=', 1)
                    ->distinct()
                    ->pluck('product_id')
                    ->toArray();

                $partialMatchProductIds = ProductSearch::whereFullText('keyword', $search_full_text_lower, ['mode' => 'boolean'])
                    ->where('is_showed', '=', 1)
                    ->whereNotIn('product_id', $exactMatchProductIds) // Исключаем уже найденные точные совпадения
                    ->distinct()
                    ->pluck('product_id')
                    ->toArray();

                $product_ids = array_merge($exactMatchProductIds, $partialMatchProductIds);

                // $product_ids = ProductSearch::where('keyword', 'like', '%' . $search_text . '%')
                //     ->distinct()
                //     ->where('is_showed', '=', 1)
                //     ->get(['product_id'])
                //     ->toArray();
            }
        }

        // dump($product_ids);

        $product_id = [];

        // if ($design == 'design_5') {
        //     foreach ($product_ids as $product) {
        //         foreach ($product as $item) {
        //             $product_id[] = $item->product_id;
        //         }
        //     }
        // } else {
            foreach ($product_ids as $item) {
                $product_id[] = $item;
            }
        // }

        if (env('APP_GIFT_CARD') == 0) {
            foreach ($product_id as $key => $val) {
                if ($val == 616) {
                    unset($product_id[$key]);
                }
            }
        }

        if (!empty($product_id)) {
            $products = Product::query()
                ->where('is_showed', '=', 1)
                ->whereIn('id', $product_id)
                ->orderByRaw('FIELD(id, ' . implode(',', $product_id) . ')')
                // ->orderBy('main_order', 'asc')
                ->get(['id', 'image', 'aktiv'])
                ->toArray();
        } else {
            $products = [];
        }

        // $products = Product::query()
        //     ->where('is_showed', '=', 1)
        //     ->whereIn('id', $product_id)
        //     ->orderByRaw('FIELD(id, ' . implode(',', $product_id) . ')')
        //     // ->orderBy('main_order', 'asc')
        //     ->get(['id', 'image', 'aktiv'])
        //     ->toArray();

        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());

        for ($i = 0; $i < count($products); $i++) {
            if (isset($products_desc[$products[$i]['id']])) {
                $products[$i]['name'] = $products_desc[$products[$i]['id']]['name'];
                $products[$i]['desc'] = $products_desc[$products[$i]['id']]['desc'];

                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $products[$i]['url'] = "Buying_".$products_desc[$products[$i]['id']]['url']."_online";
                    } else {
                        $products[$i]['url'] = __('text.text_aff_domain_1') . '_' . $products_desc[$products[$i]['id']]['url'] . '_' . __('text.text_aff_domain_2');
                    }
                } else {
                    $products[$i]['url'] = $products_desc[$products[$i]['id']]['url'];
                }

                $products[$i]['alt'] = $products[$i]['image'];

                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    $products[$i]['image'] = $domainWithoutZone . '_' . $products[$i]['image'];
                    $products[$i]['alt'] = __('text.text_aff_domain_1') . '_' . $products[$i]['name'] . '_' . __('text.text_aff_domain_2');
                }

                $products[$i]['aktiv'] = explode(',', ucwords(str_replace("\r\n", '', trim($products[$i]['aktiv']))));
                foreach ($products[$i]['aktiv'] as $key => $value) {

                    $aktive_url = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($value))));

                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $aktive_url = 'Buying_' . $aktive_url . "_online";
                    } else {
                        $aktive_url = __('text.text_aff_domain_1') . '_' . $aktive_url . "_" . __('text.text_aff_domain_2');
                    }

                    $products[$i]['aktiv'][$key] = [
                        'name' => trim($value),
                        'url' => $aktive_url
                    ];
                }
                foreach ($product_price as $key=>$pp) {
                    if ($products[$i]['id'] == $key) {
                        $products[$i]['price'] = $product_price[$products[$i]['id']]['price'];

                        if (isset($product_price[$products[$i]['id']]['discount'])) {
                            $products[$i]['discount'] = $product_price[$products[$i]['id']]['discount'];
                        }
                    }
                }
            } else {
                $products[$i]['unset'] = true;
            }
        }

        foreach ($products as $key => $val) {
            if (isset($val['unset']) && $val['unset'] == 'true'){
                unset($products[$key]);
            }
        }

        foreach ($products as $i => $product) {
            if (!isset($product['price'])) {
                $products[$i]['price'] = 0;
            }
            if (!isset($product['discount'])) {
                $products[$i]['discount'] = 0;
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
            $url = $item->url;

            if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                $url = 'Buying_' . $url . "_online";
            } else {
                $url = __('text.text_aff_domain_1') . '_' . $url . "_" . __('text.text_aff_domain_2');
            }

            $tips .= $item->name . " $category||category/" . $url . "\n";
        }

        return $tips;
    }

    public static function SearchDisease($search_text)
    {
        $disease = trans('text.common_disease_search');
        $language_id = Language::$languages[App::currentLocale()];
        // $result = ProductDisease::where("disease", "LIKE", "%$search_text%")->where('language_id', '=', $language_id)->distinct()->get('disease')->toArray();
        $result = DB::select("SELECT DISTINCT pd.disease FROM product p
                            JOIN product_disease pd ON pd.product_id = p.id
                            WHERE pd.disease LIKE ? AND pd.language_id = ? AND p.is_showed = 1",
                            ['%' . $search_text . '%', $language_id]);

        $tips = "";
        foreach($result as $item)
        {
            $url = str_replace(' ', '-', strtolower($item->disease));

            if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                $url = 'Buying_' . $url . "_online";
            } else {
                $url = __('text.text_aff_domain_1') . '_' . $url . "_" . __('text.text_aff_domain_2');
            }

            $tips .= $item->disease . " $disease||disease/" . $url . "\n";
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

                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $url = 'Buying_' . $url . "_online";
                } else {
                    $url = __('text.text_aff_domain_1') . '_' . $url . "_" . __('text.text_aff_domain_2');
                }

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

                        // $pos = strripos($s, "'");
                        // if ($pos > 0) {
                        //     $s = substr_replace($s, "\/", $pos - 1);
                        // }
                        // var_dump($s);
                        $result[] = preg_replace('/[^A-Za-z0-9\s\-\']/', '', $s); // [^A-Za-z0-9\s\-]
                    }
                }
            }

            $result = array_unique($result);

            foreach($result as $item)
            {
                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $tips .= $item . "||Buying_" . Str::lower(str_replace(' ', '-', $item)) . "_online.html\n";
                    } else {
                        $tips .= $item . "||" . __('text.text_aff_domain_1') . '_' . Str::lower(str_replace(' ', '-', $item)) . '_' . __('text.text_aff_domain_2') . ".html\n";
                    }
                } else {
                    $tips .= $item . "||" . Str::lower(str_replace(' ', '-', $item)) . ".html\n";
                }
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
        if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
            $domain = substr($domain, 0, -1);
        }

        $domain = request()->getHost();

        $language_id = Language::$languages[App::currentLocale()];

        $page_properties = DB::select("SELECT * FROM page_properties WHERE `page` = '$page' AND `language` = $language_id");

        if (isset($page_properties[0])) {
            $page_properties = $page_properties[0];

            $page_properties->title = str_replace('(host_name)', $domain, $page_properties->title);
            $page_properties->keyword = str_replace('(host_name)', $domain, $page_properties->keyword);
            $page_properties->description = str_replace('(host_name)', $domain, $page_properties->description);
        } else {
            $page_properties = (object)$page_properties;

            $page_properties->title = '';
            $page_properties->keyword = '';
            $page_properties->description = '';
        }

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
            case 'sitemap':

                $page_properties->title = __('text.menu_title_sitemap') . ' - ' . request()->getHost();
                $page_properties->keyword = 'Online pharmacy, certified pharmacy, online drugs, pharmacy meds, order medicines online, pharmacies mail order, verified online pharmacy, reputable pharmacy online, drugstore online, meds online, generic pharmacy, discount pharmacy, non prescription pharmacy, legitimate pharmacy online';
                $page_properties->description = request()->getHost() . ' - Discount Pharmacy Store. Big Sales. High quality products. Fast worldwide shipping.';
        }

        return $page_properties;
    }

    public static function getProductProperties($product) {
        $domain = str_replace(['http://', 'https://'], '', env('APP_URL'));
        $last_char = strlen($domain) - 1;

        if (!empty($domain)) {
            if (isset($domain[$last_char]) && $domain[$last_char] == '/') {
                $domain = substr($domain, 0, -1);
            }
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

        // $product_properties_new = DB::select("SELECT `title`, `keywords`, `description` FROM product_desc WHERE `url` = '$product' AND `language_id` = $language_id");
        $product_properties_new = DB::table('product_desc')
            ->where('url', '=', $product)
            ->where('language_id', '=', $language_id)
            ->get(['title', 'keywords', 'description'])
            ->toArray();

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
            // $product_id = DB::select("SELECT id FROM product WHERE sinonim LIKE '%$product%' AND is_showed = 1");
            $product_id = DB::table('product')
                ->where('sinonim', 'like', '%' . $product . '%')
                ->where('is_showed', '=', 1)
                ->get(['id'])
                ->toArray();

            if (empty($product_id)) {
                $product = str_replace('-', ' ', $product);
                // $product_id = DB::select("SELECT id FROM product WHERE sinonim LIKE '%$product%' AND is_showed = 1");
                $product_id = DB::table('product')
                    ->where('sinonim', 'like', '%' . $product . '%')
                    ->where('is_showed', '=', 1)
                    ->get(['id'])
                    ->toArray();
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

        if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
            $page_properties->title = __('text.text_aff_domain_1') . '_' . $product_name . '_' . __('text.text_aff_domain_2') . ' - ' . request()->getHost();
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

    public static function getProductRecommendation($product_id) {
        $design = session('design') ? session('design') : config('app.design');

        $result = DB::table('suggested')
            ->where('product_id', '=', $product_id)
            ->orderBy('ord', 'desc')
            ->get(['product_id', 'sugg_id'])
            ->toArray();

        if (empty($result)) {
            $result2 = [];
        } else {
            foreach ($result as $m) {
                $result2[] = $m->sugg_id;
            }
        }

        $bestsellers = ProductServices::GetBestsellers($design);
        unset($bestsellers[0]);

        if (empty($result)) {
            $products = $bestsellers;
        } else {
            $products = ProductServices::getProductData( $result2, $design);

            $count_rec = count($products);

            if ($count_rec < 6) {
                $need_add = 6 - $count_rec;
                $count_add = 0;

                foreach ($products as $id => $value) {
                    foreach ($bestsellers as $key => $best) {
                        if ($need_add == $count_add) {
                            break;
                        }
                        if ($id == $best['id'] || $best['id'] == $product_id) {
                            continue;
                        } else {
                            $products[] = $best;
                            $count_add++;
                        }
                    }
                }
            }
        }

        return $products;
    }

    public static function getCartRecommendation() {
        $design = session('design') ? session('design') : config('app.design');
        $bestsellers = ProductServices::GetBestsellers($design);
        unset($bestsellers[0]);

        // $bestsellers = array_slice($bestsellers, 0, 6);

        if(empty(session('cart'))) {
            $products = $bestsellers;
        } else {
            $cart_data = session('cart');
            $products = [];

            if (count($cart_data) === 1) {
                $result = DB::table('suggested')
                    ->where('product_id', '=', $cart_data[0]['product_id'])
                    ->orderBy('ord', 'desc')
                    ->get(['product_id', 'sugg_id'])
                    ->toArray();

                if (empty($result)) {
                    $result2 = [];
                } else {
                    foreach ($result as $m) {
                        $result2[] = $m->sugg_id;
                    }
                }

                $products = ProductServices::getProductData($result2, $design);

            } elseif (count($cart_data) === 2) {
                $result = DB::table('suggested')
                    ->where('product_id', '=', $cart_data[0]['product_id'])
                    ->orderBy('ord', 'desc')
                    ->get(['product_id', 'sugg_id'])
                    ->toArray();

                if (empty($result)) {
                    $result2 = [];
                } else {
                    foreach ($result as $m) {
                        $result2[] = $m->sugg_id;
                    }
                }

                $result2 = array_slice($result2, 0, 3);

                $result3 = DB::table('suggested')
                    ->where('product_id', '=', $cart_data[1]['product_id'])
                    ->orderBy('ord', 'desc')
                    ->get(['product_id', 'sugg_id'])
                    ->toArray();

                if (empty($result3)) {
                    $result4 = [];
                } else {
                    foreach ($result3 as $m) {
                        $result4[] = $m->sugg_id;
                    }
                }

                $result4 = array_slice($result4, 0, 3);

                $products = array_unique( array_merge($result2, $result4));

                foreach($products as $k => $v) {
                    if ($v == $cart_data[0]['product_id'] || $v == $cart_data[1]['product_id']) {
                        unset($products[$k]);
                    }
                }

                $products = ProductServices::getProductData($products, $design);

            } elseif (count($cart_data) === 3) {

                    $result = DB::table('suggested')
                    ->where('product_id', '=', $cart_data[0]['product_id'])
                    ->orderBy('ord', 'desc')
                    ->get(['product_id', 'sugg_id'])
                    ->toArray();

                if (empty($result)) {
                    $result2 = [];
                } else {
                    foreach ($result as $m) {
                        $result2[] = $m->sugg_id;
                    }
                }

                $result2 = array_slice($result2, 0, 3);

                $result3 = DB::table('suggested')
                    ->where('product_id', '=', $cart_data[1]['product_id'])
                    ->orderBy('ord', 'desc')
                    ->get(['product_id', 'sugg_id'])
                    ->toArray();

                if (empty($result3)) {
                    $result4 = [];
                } else {
                    foreach ($result3 as $m) {
                        $result4[] = $m->sugg_id;
                    }
                }

                $result4 = array_slice($result4, 0, 3);

                $result5 = DB::table('suggested')
                    ->where('product_id', '=', $cart_data[2]['product_id'])
                    ->orderBy('ord', 'desc')
                    ->get(['product_id', 'sugg_id'])
                    ->toArray();

                if (empty($result5)) {
                    $result6 = [];
                } else {
                    foreach ($result5 as $m) {
                        $result6[] = $m->sugg_id;
                    }
                }

                $result4 = array_slice($result6, 0, 3);

                $products = array_unique( array_merge($result2, $result4, $result6));

                foreach($products as $k => $v) {
                    if ($v == $cart_data[0]['product_id'] || $v == $cart_data[1]['product_id'] || $v == $cart_data[2]['product_id']) {
                        unset($products[$k]);
                    }
                }

                $products = ProductServices::getProductData($products, $design);

            } elseif (count($cart_data) > 3) {
                $products = array_merge($products, $bestsellers);

                foreach ($products as $key => $product_data) {
                    foreach ($cart_data as $k => $data) {
                        if ($data['product_id'] == $product_data['id']) {
                            unset($products[$key]);
                        }
                    }
                }
            }

            // Проверяем, хватает ли предложек
            $needed_count = 6 - count($products);

            if ($needed_count > 0) {
                // Можем взять дополнительные товары из бестселлеров, если это необходимо
                foreach ($bestsellers as $best) {
                    if ($needed_count <= 0) {
                        break;
                    }
                    if (!in_array($best['id'], array_column($products, 'id')) && !in_array($best['id'], array_column($cart_data, 'product_id'))) {
                        $products[] = $best;
                        $needed_count--;
                    }
                }
            }
        }

        return $products;
    }

    public static function getProductData($products_arr, $design) {
        $product_price = self::GetAllProductPillPrice($design);
        $products = [];

        if ($design == 5) {
            $product_data = DB::table('product')
                ->join('product_desc', 'product.id', '=', 'product_desc.product_id')
                ->join('product_category', 'product.id', '=', 'product_category.product_id')
                ->whereIn('product.id', $products_arr)
                ->where('product.is_showed', '=', 1)
                ->where('product_category.category_id', '=',14)
                ->where('product_desc.language_id', '=', Language::$languages[App::currentLocale()])
                ->get(['product.id', 'product_desc.name', 'product_desc.url', 'product_desc.desc', 'product.aktiv', 'product.image'])
                ->keyBy('id')
                ->toArray();
        } else {
            $product_data = DB::table('product')
                ->join('product_desc', 'product.id', '=', 'product_desc.product_id')
                ->whereIn('product.id', $products_arr)
                ->where('product.is_showed', '=', 1)
                ->where('product_desc.language_id', '=', Language::$languages[App::currentLocale()])
                ->get(['product.id', 'product_desc.name', 'product_desc.url', 'product_desc.desc', 'product.aktiv', 'product.image'])
                ->keyBy('id')
                ->toArray();
        }

        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());

        foreach ($products_arr as $product_id) {
            if (isset($product_data[$product_id])) {

                if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) {
                    if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                        $new_rec_url = 'Buying_'.$product_data[$product_id]->url.'_online';
                    } else {
                        $new_rec_url = __('text.text_aff_domain_1') . '_' . $product_data[$product_id]->url . '_' . __('text.text_aff_domain_2');
                    }
                } else {
                    $new_rec_url = $product_data[$product_id]->url;
                }

                $products[$product_id] = [
                    'id' => $product_id,
                    'name' => $product_data[$product_id]->name,
                    'url' =>  $new_rec_url,
                    'desc' => $product_data[$product_id]->desc,
                    'aktiv' =>  explode(',', str_replace("\r\n", '', ucwords(trim($product_data[$product_id]->aktiv)))),
                    'price' => isset($product_price[$product_id]['price']) ? $product_price[$product_id]['price'] : 0,
                    'discount' => isset($product_price[$product_id]['discount']) ? $product_price[$product_id]['discount'] : 0,
                    'image' => in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]) ? $domainWithoutZone.'_'.$product_data[$product_id]->image : $product_data[$product_id]->image,
                    'alt' => in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]) ? __('text.text_aff_domain_1') . '_' . $product_data[$product_id]->name . '_' . __('text.text_aff_domain_2') : $product_data[$product_id]->image
                ];
            }
        }

        foreach ($products as $key => $value) {
            foreach ($products[$key]['aktiv'] as $k => $val) {

                $aktive_url = str_replace('&', '-', str_replace(' ', '-', strtolower(trim($val))));

                if (in_array(App::currentLocale(), ['hant', 'hans', 'gr', 'arb', 'ja'])) {
                    $aktive_url = 'Buying_' . $aktive_url . "_online";
                } else {
                    $aktive_url = __('text.text_aff_domain_1') . '_' . $aktive_url . "_" . __('text.text_aff_domain_2');
                }

                $products[$key]['aktiv'][$k] = [
                    'name' => trim($val),
                    'url' => $aktive_url
                ];
            }
        }

        return $products;
    }

    public static function getDataRecommendation() {
        $result = Http::timeout(3)->post('http://true-services.net/product_recommendation/product_rec.php');
        $result = json_decode($result, true);

        foreach ($result as $k => $value) {
            $result[$k] = array_slice($value, 0, 6, true);
        }

        $current_ord = [];
        foreach ($result as $product_id => $products) {

            if (!isset($current_ord[$product_id])) {
                $current_ord[$product_id] = count($products) * 10;
            }

            foreach ($products as $sugg_id => $val) {
                DB::table('suggested')->insert([
                    'product_id' => $product_id,
                    'sugg_id' => $sugg_id,
                    'ord' => $current_ord[$product_id]
                ]);
                $current_ord[$product_id] -= 10;
            }
        }
    }
}