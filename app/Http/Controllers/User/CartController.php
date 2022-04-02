<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
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
            $lineItem = [
                'name' => $product->name,
                'description' => $product->information,
                'amount' => $product->price,
                'currency' => 'jpy',
                'quantity' => $product->pivot->quantity,
            ];

            array_push($lineItems, $lineItem);
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => route('user.items.index'),
            'cancel_url' => route('user.cart.index'),
        ]);

        $publicKey = env('STRIPE_PUBLIC_KEY');

        return view('user.checkout', compact('session', 'publicKey'));
    }
}
