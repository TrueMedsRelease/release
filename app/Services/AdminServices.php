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
                ->get(['product_packaging.id', 'product_packaging.dosage', 'product_packaging.num', 'product_type.name'])
                ->toArray();
        } else {
            $packaging = [];
        }

        return $packaging;
    }
}