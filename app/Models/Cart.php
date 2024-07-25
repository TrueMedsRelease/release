<?php

namespace App\Models;

use App\Services\ProductServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    public static function add($pack_id)
    {
        $product_pack = ProductPackaging::query()->find($pack_id);
        $array = [];
        $upgrade_pack = ProductServices::GetUpgradePack($pack_id);
        $max_pill_price = ProductServices::GetMaxPriceForPill($product_pack->product_id, $product_pack->dosage);
        $product = [
            'cart_id' => session()->getId(),
            'pack_id' => $product_pack->id,
            'product_id' => $product_pack->product_id,
            'dosage' => $product_pack->dosage,
            'num' => $product_pack->num,
            'type' => $product_pack->type_id,
            'q' => 1,
            'price' => $product_pack->price,
            'max_pill_price' => $max_pill_price,
            'upgrade_pack' => $upgrade_pack
        ];

        if(empty(session('cart')))
        {
            $array[] = $product;
            session(['cart' => $array]);
        }
        else
        {
            $products = session('cart');
            $new_product = true;
            foreach($products as $item)
            {
                if($item['pack_id'] == $pack_id)
                {
                    $new_product = false;
                    break;
                }
            }
            if($new_product)
            {
                $products[] = $product;
            }
            else
            {
                foreach($products as &$item)
                {
                    if($item['pack_id'] == $pack_id)
                    {
                        $item['q']++;
                        break;
                    }
                }
            }
            session(['cart' => $products]);
        }
    }

    public static function decrease($pack_id)
    {
        $products = session('cart');
        foreach($products as &$product)
        {
            if($product['pack_id'] == $pack_id)
            {
                $product['q'] = $product['q'] == 1 ? $product['q'] : $product['q'] -= 1;
                break;
            }
        }
        unset($product);

        session(['cart' => $products]);
    }

    public static function remove($pack_id)
    {
        $products = session('cart');

        if(count($products) > 1)
        {
            for($i = 0; $i < count($products); $i++)
            {
                if($products[$i]['pack_id'] == $pack_id)
                {
                    unset($products[$i]);
                    break;
                }
            }
            session(['cart' => $products]);
        }
        else
        {
            session()->forget('cart');
        }
    }

    public static function upgrade($pack_id)
    {
        $products = session('cart');
        $pack = ProductPackaging::query()->find($pack_id);

        $product_pack = ProductPackaging::query()
                        ->where('product_id', '=', $pack->product_id)
                        ->where('dosage', '=', $pack->dosage)
                        ->where('price', '!=', 0)
                        ->where('is_showed', '=', 1)
                        ->where('num', '>', $pack->num)
                        ->orderBy('num', 'asc')
                        ->limit(1)
                        ->get()
                        ->toArray();
                        
        $product_pack = $product_pack[0];

        $array = [];
        $upgrade_pack = ProductServices::GetUpgradePack($product_pack['id']);
        $max_pill_price = ProductServices::GetMaxPriceForPill($product_pack['product_id'], $product_pack['dosage']);
        $product = [
            'cart_id' => session()->getId(),
            'pack_id' => $product_pack['id'],
            'product_id' => $product_pack['product_id'],
            'dosage' => $product_pack['dosage'],
            'num' => $product_pack['num'],
            'type' => $product_pack['type_id'],
            'q' => 1,
            'price' => $product_pack['price'],
            'max_pill_price' => $max_pill_price,
            'upgrade_pack' => $upgrade_pack
        ];

        for($i = 0; $i < count($products); $i++)
        {
            if($products[$i]['pack_id'] == $pack_id)
            {
                $products[$i] = $product;
                break;
            }
        }
        session(['cart' => $products]);

    }

}