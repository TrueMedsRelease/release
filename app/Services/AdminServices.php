<?php

namespace App\Services;

use App\Models\Product;
use App\Services\ProductServices;
use Illuminate\Support\Facades\DB;

class AdminServices
{
    public static function getNotShowedOnMainProduct() {
        $products_desc = ProductServices::GetProductDesc(1);

        $not_showed_product = Product::query()
            ->where('is_showed_on_main', '=', 0)
            ->where('is_showed', '=', 1)
            ->orderBy('main_order', 'asc')
            ->get(['id'])
            ->toArray();

        for ($i = 0; $i < count($not_showed_product); $i++) {
            $not_showed_product[$i]['name'] = $products_desc[$not_showed_product[$i]['id']]['name'];
            $not_showed_product[$i]['url'] = $products_desc[$not_showed_product[$i]['id']]['url'];
        }

        return $not_showed_product;
    }

    public static function getShowedOnMainProduct() {
        $products_desc = ProductServices::GetProductDesc(1);

        $showed_product = Product::query()
            ->where('is_showed_on_main', '=', 1)
            ->where('is_showed', '=', 1)
            ->orderBy('main_order', 'asc')
            ->get(['id'])
            ->toArray();

        for ($i = 0; $i < count($showed_product); $i++) {
            $showed_product[$i]['name'] = $products_desc[$showed_product[$i]['id']]['name'];
            $showed_product[$i]['url'] = $products_desc[$showed_product[$i]['id']]['url'];
        }

        return $showed_product;
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