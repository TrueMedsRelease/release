<?php

namespace App\Services;

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AdminServices
{

    public static function hasMainPageOrderTable(): bool
    {
        static $hasTable = null;

        if ($hasTable !== null) {
            return $hasTable;
        }

        if (Schema::hasTable('main_page_order')) {
            $hasTable = true;
            return true;
        }

        try {
            Schema::create('main_page_order', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('product_id')->unique();
                $table->tinyInteger('is_showed_on_main')->default(0);
                $table->unsignedInteger('main_order')->default(0);

                $table->index(['is_showed_on_main', 'main_order']);
            });
        } catch (\Throwable $e) {
            // Если таблицу уже создал другой запрос или у пользователя БД нет прав CREATE,
            // повторно проверяем наличие таблицы. Если её всё равно нет, код ниже
            // продолжит работать по старой логике через product.
        }

        $hasTable = Schema::hasTable('main_page_order');

        return $hasTable;
    }

    protected static function getProductMainPageBaseValues(int $productId)
    {
        return DB::table('product')
            ->where('id', '=', $productId)
            ->first(['is_showed_on_main', 'main_order']);
    }

    public static function setMainPageProductShowed(int $productId, int $isShowedOnMain): void
    {
        $isShowedOnMain = $isShowedOnMain ? 1 : 0;

        if (static::hasMainPageOrderTable()) {
            $product = static::getProductMainPageBaseValues($productId);

            if (!$product) {
                return;
            }

            $mainPageOrder = DB::table('main_page_order')
                ->where('product_id', '=', $productId)
                ->first(['main_order']);

            DB::table('main_page_order')->updateOrInsert(
                ['product_id' => $productId],
                [
                    'is_showed_on_main' => $isShowedOnMain,
                    'main_order' => $mainPageOrder
                        ? (int) $mainPageOrder->main_order
                        : (int) $product->main_order,
                ]
            );

            return;
        }

        DB::table('product')
            ->where('id', '=', $productId)
            ->update(['is_showed_on_main' => $isShowedOnMain]);
    }

    public static function getMainPageOrderValue(int $productId): int
    {
        if (static::hasMainPageOrderTable()) {
            $product = DB::table('product')
                ->leftJoin('main_page_order as mpo', 'product.id', '=', 'mpo.product_id')
                ->where('product.id', '=', $productId)
                ->where('product.is_showed', '=', 1)
                ->selectRaw('COALESCE(mpo.main_order, product.main_order) as main_order')
                ->first();

            return $product ? (int) $product->main_order : 0;
        }

        $product = DB::table('product')
            ->where('id', '=', $productId)
            ->where('is_showed', '=', 1)
            ->first(['main_order']);

        return $product ? (int) $product->main_order : 0;
    }

    public static function setMainPageOrderValue(int $productId, int $mainOrder): void
    {
        if (static::hasMainPageOrderTable()) {
            $product = static::getProductMainPageBaseValues($productId);

            if (!$product) {
                return;
            }

            $mainPageOrder = DB::table('main_page_order')
                ->where('product_id', '=', $productId)
                ->first(['is_showed_on_main']);

            DB::table('main_page_order')->updateOrInsert(
                ['product_id' => $productId],
                [
                    'is_showed_on_main' => $mainPageOrder
                        ? (int) $mainPageOrder->is_showed_on_main
                        : (int) $product->is_showed_on_main,
                    'main_order' => $mainOrder,
                ]
            );

            return;
        }

        DB::table('product')
            ->where('id', '=', $productId)
            ->update(['main_order' => $mainOrder]);
    }

    public static function getNotShowedOnMainProduct() {
        $products_desc = ProductServices::GetProductDesc(1);

        $query = Product::query()
            ->where('product.is_showed', '=', 1)
            ->when(Schema::hasColumn('product', 'ban'), function ($q) {
                $q->where('product.ban', '=', 0);
            });

        if (static::hasMainPageOrderTable()) {
            $query->leftJoin('main_page_order as mpo', 'product.id', '=', 'mpo.product_id')
                ->whereRaw('COALESCE(mpo.is_showed_on_main, product.is_showed_on_main) = ?', [0])
                ->orderByRaw('COALESCE(mpo.main_order, product.main_order) ASC')
                ->select('product.id');
        } else {
            $query->where('product.is_showed_on_main', '=', 0)
                ->orderBy('product.main_order', 'asc')
                ->select('product.id');
        }

        $not_showed_product = $query->get()->toArray();

        for ($i = 0; $i < count($not_showed_product); $i++) {
            if (!isset($products_desc[$not_showed_product[$i]['id']])) {
                unset($not_showed_product[$i]);
                continue;
            }

            $not_showed_product[$i]['name'] = $products_desc[$not_showed_product[$i]['id']]['name'];
            $not_showed_product[$i]['url'] = $products_desc[$not_showed_product[$i]['id']]['url'];
        }

        return array_values($not_showed_product);
    }

    public static function getShowedOnMainProduct() {
        $products_desc = ProductServices::GetProductDesc(1);

        $query = Product::query()
            ->where('product.is_showed', '=', 1)
            ->when(Schema::hasColumn('product', 'ban'), function ($q) {
                $q->where('product.ban', '=', 0);
            });

        if (static::hasMainPageOrderTable()) {
            $query->leftJoin('main_page_order as mpo', 'product.id', '=', 'mpo.product_id')
                ->whereRaw('COALESCE(mpo.is_showed_on_main, product.is_showed_on_main) = ?', [1])
                ->orderByRaw('COALESCE(mpo.main_order, product.main_order) ASC')
                ->select('product.id');
        } else {
            $query->where('product.is_showed_on_main', '=', 1)
                ->orderBy('product.main_order', 'asc')
                ->select('product.id');
        }

        $showed_product = $query->get()->toArray();

        for ($i = 0; $i < count($showed_product); $i++) {
            if (!isset($products_desc[$showed_product[$i]['id']])) {
                unset($showed_product[$i]);
                continue;
            }

            $showed_product[$i]['name'] = $products_desc[$showed_product[$i]['id']]['name'];
            $showed_product[$i]['url'] = $products_desc[$showed_product[$i]['id']]['url'];
        }

        return array_values($showed_product);
    }

    public static function getCategoriesWithUnavailableProducts() {
        $all_category_ids = DB::table('category')
            ->distinct()
            ->where('is_showed', '=', 1)
            ->orderBy('ord', 'asc')
            ->get(['id', 'en_name', 'ord'])
            ->toArray();

        $not_showed_products_info = [];
        foreach ($all_category_ids as $cur_category_id){
            $not_showed_products_id = DB::table('product')
                ->distinct()
                ->join('product_desc', 'product.id', '=', 'product_desc.product_id')
                ->join('product_category', 'product.id', '=', 'product_category.product_id')
                ->where('product.is_showed', '=', 0)
                ->when(Schema::hasColumn('product', 'ban'), function ($q) {
                    $q->where('product.ban', '=', 0);
                })
                ->where('product_category.category_id', '=', $cur_category_id->id)
                ->whereNotIn('product.id', [615, 224, 223, 225])
                ->orderBy('product_desc.name')
                ->get(['product.id', 'product_desc.name', 'product.main_order'])
                ->toArray();

            if(count($not_showed_products_id)){
                array_push($not_showed_products_info, ["id" => 0, "name" => ".......".$cur_category_id->en_name."......"]);
                foreach ($not_showed_products_id as $cur_product_id){
                    array_push($not_showed_products_info, ["id" => $cur_product_id->id, "name" => $cur_product_id->name]);
                }
            }
        }

        return $not_showed_products_info;
    }

    public static function getCategoriesWithAvailableProducts() {
        $all_category_ids = DB::table('category')
            ->distinct()
            ->where('is_showed', '=', 1)
            ->orderBy('ord', 'asc')
            ->get(['id', 'en_name', 'ord'])
            ->toArray();

        $showed_products_info = [];
        foreach ($all_category_ids as $cur_category_id){
            $showed_products_id = DB::table('product')
                ->distinct()
                ->join('product_desc', 'product.id', '=', 'product_desc.product_id')
                ->join('product_category', 'product.id', '=', 'product_category.product_id')
                ->where('product.is_showed', '=', 1)
                ->when(Schema::hasColumn('product', 'ban'), function ($q) {
                    $q->where('product.ban', '=', 0);
                })
                ->where('product_category.category_id', '=', $cur_category_id->id)
                ->whereNotIn('product.id', [615, 224, 223, 225])
                ->orderBy('product_desc.name')
                ->get(['product.id', 'product_desc.name', 'product.main_order'])
                ->toArray();

            if(count($showed_products_id)){
                array_push($showed_products_info, ["id" => 0, "name" => ".......".$cur_category_id->en_name."......"]);
                foreach ($showed_products_id as $cur_product_id){
                    array_push($showed_products_info, ["id" => $cur_product_id->id, "name" => $cur_product_id->name]);
                }
            }
        }

        return $showed_products_info;
    }

    public static function getAllProductWithCategory() {
        $all_category_ids = DB::table('category')
            ->distinct()
            ->where('is_showed', '=', 1)
            ->orderBy('ord', 'asc')
            ->get(['id', 'en_name', 'ord'])
            ->toArray();

        $products_info = [];
        foreach ($all_category_ids as $cur_category_id){
            $products_id = DB::table('product')
                ->distinct()
                ->join('product_desc', 'product.id', '=', 'product_desc.product_id')
                ->join('product_category', 'product.id', '=', 'product_category.product_id')
                ->where('product_category.category_id', '=', $cur_category_id->id)
                ->when(Schema::hasColumn('product', 'ban'), function ($q) {
                    $q->where('product.ban', '=', 0);
                })
                ->whereNotIn('product.id', [615, 224, 223, 225])
                ->orderBy('product_desc.name')
                ->get(['product.id', 'product_desc.name', 'product.main_order'])
                ->toArray();

            if(count($products_id)){
                array_push($products_info, ["id" => 0, "name" => ".......".$cur_category_id->en_name."......"]);
                foreach ($products_id as $cur_product_id){
                    array_push($products_info, ["id" => $cur_product_id->id, "name" => $cur_product_id->name]);
                }
            }
        }

        return $products_info;
    }

    public static function getShowedProductPackaging($product_id, $is_showed) {

        if ($product_id != 0) {
            $packaging = DB::table("product_packaging")
                ->join('product_type', 'product_packaging.type_id', '=', 'product_type.id')
                ->where('product_packaging.is_showed', '=', $is_showed)
                ->where('product_packaging.price', '>', 0)
                ->where('product_packaging.product_id', '=', $product_id)
                ->orderBy('product_packaging.ord')
                ->get(['product_packaging.id', 'product_packaging.dosage', 'product_packaging.num', 'product_packaging.min_price', 'product_packaging.price', 'product_type.name'])
                ->toArray();
        } else {
            $packaging = [];
        }

        return $packaging;
    }

    public static function getProductProperties($product_id) {
        if ($product_id != 0) {

            $product = DB::table('product')
                ->where('id', '=', $product_id)
                ->when(Schema::hasColumn('product', 'ban'), function ($q) {
                    $q->where('ban', '=', 0);
                })
                ->get(['is_showed', 'sinonim'])
                ->toArray();
            $product = $product[0];

            $product->sinonim = preg_replace('/\b(\w.+)\b/', '$0/', $product->sinonim);
            $product->sinonim = str_replace("\u{FEFF}", '', $product->sinonim);
            $product->sinonim = explode('/', $product->sinonim);
            if ($product->sinonim[0] == "﻿" || $product->sinonim[0] == "")
            for ($i = 0; $i < count($product->sinonim); $i++)
                if ($i+1 != count($product->sinonim))
                    $product->sinonim[$i] = $product->sinonim[$i+1];
            if ($product->sinonim[0] != "﻿" || $product->sinonim[0] != "")
            for ($i = 0; $i < count($product->sinonim); $i++)
                $product->sinonim[$i] = $product->sinonim[$i];

            foreach($product->sinonim as $i => $ps) {
                if ($ps != "") {
                    $product->sinonim[$i] = trim(str_replace('-', ' ', str_replace("\u{FEFF}", '', $ps)));
                }
            }

            $product->sinonim = array_filter($product->sinonim);

            $product_info = DB::table('product_desc')
                    ->where('product_id', '=', $product_id)
                    ->get(['language_id', 'name', 'desc', 'url', 'title', 'keywords', 'description'])
                    ->toArray();

            $names = [];
            $descriptions = [];
            $seo = [];
            $urls = [];
            foreach ($product_info as $product_val) {
                $names[$product_val->language_id] = $product_val->name;
                $descriptions[$product_val->language_id] = trim($product_val->desc);
                $urls[$product_val->language_id] = $product_val->url;
                $seo[$product_val->language_id] = [
                    'title' => $product_val->title == null ? '' : $product_val->title,
                    'keywords' => $product_val->keywords == null ? '' : $product_val->keywords,
                    'description' => $product_val->description == null ? '' : $product_val->description
                ];
            }

            $product->names = $names;
            $product->descriptions = $descriptions;
            $product->urls = $urls;
            $product->seo = $seo;

            $dosage_list = DB::select("SELECT dosage FROM product_dosage WHERE product_id = $product_id");
            $dosage_list_arr = [];
            foreach ($dosage_list as $val) {
                $dosage_list_arr[(int)$val->dosage] = $val->dosage;
            }

            krsort($dosage_list_arr);

            $packaging_list = AdminServices::getShowedProductPackaging($product_id, 1);
            $product_packaging_info = [];

            foreach ($dosage_list_arr as $dosage_value) {
                foreach ($packaging_list as $packaging) {
                    if ($dosage_value == $packaging->dosage) {
                        $product_packaging_info[$packaging->dosage][] = [
                            'pack_id' => $packaging->id,
                            'num' => $packaging->num,
                            'price' => $packaging->price,
                            'min_price' => $packaging->min_price
                        ];
                    }
                }
            }

            $product->packaging_info = $product_packaging_info;

        } else {
            $product = [];
        }

        return $product;
    }

    public static function getProductUrl($product_id) {
        if ($product_id != 0) {

            $product_info = DB::table('product_desc')
                    ->where('product_id', '=', $product_id)
                    ->get(['language_id', 'name', 'desc', 'url', 'title', 'keywords', 'description'])
                    ->toArray();


            $urls = [];
            foreach ($product_info as $product_val) {
                $urls[$product_val->language_id] = $product_val->url;
            }

        } else {
            $url = [];
        }

        return $urls;
    }
}