<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Cart;
use InterventionImage;

class CartService
{
    public static function getItemsInCart($items)
    {
        // Storage::putFile('public/shops', $imageFile); リサイズなしであればputFileメソッドで実行できる
        //Interventionを使用するとfileをファイルから画像に変換してしまうので、putFileが適応できなくなってしまう。

        $products =[];
        foreach($items as $item){
            $p = Product::findOrFail($item->product_id);
            //オーナーの名前とメールを取得
            $owner = $p->shop->owner->select('name', 'email')->first()->toArray();//オーナー情報
            $values = array_values($owner); //オーナー情報を連想配列の値を取得
            $key = ['ownerName', 'email'];//オーナー情報のnameをownerNameに書き換える
            $ownerInfo = array_combine($key, $values);//オーナー情報のキーを変更
            $product = Product::where('id', $item->product_id)
            ->select('id', 'name', 'price')->get()->toArray();//商品情報の配列
            $quantity = Cart::where('product_id', $item->product_id)
                ->select('quantity')->get()->toArray();//在庫情報の配列
            $result = array_merge($product[0], $ownerInfo, $quantity[0]);//配列の結合
            array_push($products, $result);//配列に追加

        }
        return $products;

    }
}
