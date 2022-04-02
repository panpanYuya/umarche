<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function index(){
        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        $totalPrice = 0;

        //合計料金を計算する処理
        foreach($products as $product){
            $totalPrice += $product->price * $product->pivot->quantity;
        }

        return view('user.cart', compact('products', 'totalPrice'));
    }

    public function add(Request $request){

        $itemInCart = Cart::where('user_id',Auth::id())->where('product_id', $request->product_id)->first();//カートに商品があるかを確認する。
        if($itemInCart){
            $itemInCart->quantity += $request->quantity;//あれば数量を追加
            $itemInCart->save();
        } else {
            Cart::create([//なければ新規作成
                'user_id' =>Auth::id(),
                'product_id' =>$request->product_id,
                'quantity' => $request->quantity
            ]);
        }
        return redirect()->route('user.cart.index');

    }

    public function delete($id){
        Cart::where('product_id', $id)->where('user_id', Auth::id())->delete();

        return redirect()->route('user.cart.index');
    }

    public function checkout(){
        $user = User::findOrFail(Auth::id());
        $products = $user->products;

        $lineItems = [];

        foreach ($products as $product){
            $quantity = '';
            $quantity = Stock::where('product_id', $product->id)->sum('quantity');

            //決済実行時に商品の在庫が足りなかった場合はカートの中身画面に画面をもどす。
            if($product->pivot->quantity > $quantity){
                //redirectすることで以前入力されていた値を画面に表示することができる。
                return redirect()->route('user.cart.index');
            }
            $lineItem = [
                'name' => $product->name,
                'description' => $product->information,
                'amount' => $product->price,
                'currency' => 'jpy',
                'quantity' => $product->pivot->quantity,
            ];

            array_push($lineItems, $lineItem);


        }

        foreach($products as $product){
            Stock::create([
                'product_id' => $product->id,
                'type' => \Consts::PRODUCT_LIST['reduce'],
                'quantity' => $product->pivot->quantity * -1,
            ]);
        }

        // dd('test');

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => route('user.cart.success'),
            'cancel_url' => route('user.cart.index'),
        ]);
        return redirect($session->url, 303);
    }

    public function success(){
        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('user.items.index');
    }
}
