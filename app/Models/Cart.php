<?php

namespace App\Models;

use App\Services\ProductServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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
            $products = array_values($products);
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

        $count = 1;
        foreach ($products as $key => $val) {
            if($val['pack_id'] == $pack_id)
            {
                $count = $val['q'];
                break;
            }
        }

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
            'q' => $count,
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

    public static function ClacInsurance()
    {
        $products = session('cart', []);
        $insurance = 0;
        if(!empty($products))
        {
            foreach($products as $product)
            {
                if($product['dosage'] != '1card')
                {
                    $insurance += round(($product['price'] * 0.1) * $product['q'],2);
                }
            }
            $insurance += session('cart_option')['bonus_price'] * 0.1;
        }

        $insurance = $insurance < 9.99 ? 9.99 : $insurance;

        return $insurance;
    }

    public static function update_cart_total()
    {
        if(!empty(session('cart')))
        {
            $products = session('cart');
            $product_total = 0;
            foreach($products as $product)
            {
                $product_total += $product['price'] * $product['q'];
            }
        }
        else
        {
            $product_total = 0;
        }

        if(!empty(session("cart_option")))
        {
            $options = session('cart_option');
            $shipping_total = $options['shipping_price'];
            $bonus_total = $options['bonus_price'];
            $insurance = $options['insurance'] ? $options['insurance_price'] : 0;
            $secret_package = $options['secret_package'] ? $options['secret_price'] : 0;
        }
        else
        {
            $shipping_total = 0;
            $bonus_total = 0;
            $insurance = 0;
            $secret_package = 0;
        }

        $product_total += $bonus_total;

        if(!empty($coupon = session('coupon')))
        {
            if($coupon['type'] == 'coupon')
            {
                $coupon_discount = ceil($product_total * ($coupon['percent']) / 100);
            }
        }
        elseif(session()->has('coupon_get'))
        {
            $data = [
                'method' => 'coupon',
                'api_key' => '7c73d5ca242607050422af5a4304ef71',
                'coupon' => session('coupon_get'),
            ];

            $response = Http::timeout(3)->post('http://true-services.net/checkout/order.php', $data);

            $response = json_decode($response, true);

            if ($response['status'] == 'success') {
                if ($response['coupon']['type'] == 'coupon') {
                    $result['coupon'] = session('coupon_get');
                    $result['percent'] = $response['coupon']['percent'];
                    $result['type'] = $response['coupon']['type'];

                    session(['coupon' => $result]);

                    if($result['type'] == 'coupon')
                    {
                        $coupon_discount = ceil($product_total * ($result['percent']) / 100);
                    }
                }
            } else {
                session()->forget('coupon_get');
                $coupon_discount = 0;
            }

        }
        else
        {
            $coupon_discount = 0;
        }

        $has_card = 0;
        $sum_card = 0;
        $is_only_card = 0;
        $count_card = 0;
        foreach ($products as $product) {
            if ($product['product_id'] == 616) {
                $has_card = 1;
                $sum_card += $product["price"];
                $count_card++;
            }
        }

        if ($has_card && $count_card == count($products) && (int)session('cart_option')['bonus_id'] == 0) {
            $is_only_card = 1;
        }

        if ($is_only_card) {
            $all = $product_total;//+ $bonus_total

            $all_in_currency = Currency::SumInCurrency([
                Currency::Convert($product_total, true),
                // Currency::Convert($bonus_total, true),
            ]);
        } else {

            $all = $product_total + $shipping_total;//+ $bonus_total

            $all_in_currency = Currency::SumInCurrency([
                Currency::Convert($product_total, true),
                Currency::Convert($shipping_total),
                // Currency::Convert($bonus_total, true),
            ]);
        }

        $checkout_total = $all + $insurance + $secret_package - $coupon_discount;
        $eur = Currency::GetCoef('eur');

        $cart_total = [
            "product_total" => $product_total,
            "shipping_total" => $shipping_total,
            "bonus_total" => $bonus_total,
            "insurance" => $insurance,
            "secret_package" => $secret_package,
            'coupon_discount' => $coupon_discount,
            'checkout_total' => $checkout_total,
            'checkout_total_eur' => round($checkout_total * $eur,2),
            'checkout_total_in_currency' => Currency::SumInCurrency([
                Currency::Convert($all, true),
                Currency::Convert($insurance),
                Currency::Convert($secret_package),
                Currency::Convert($coupon_discount * (-1)),
            ]),
            "all" => $all,
            "all_in_currency" => $all_in_currency,
        ];

        session(['total' => $cart_total]);
    }

}
