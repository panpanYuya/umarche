<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\SendThanksMail;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Models\PrimaryCategory;
use Illuminate\Http\Request;

class ItemController extends Controller{

    public function __construct(){
        $this->middleware('auth:users');
        $this->middleware(function ($request, $next) {
            //route(show/{item})のitemを下記で取得している
            $id = $request->route()->parameter("item"); //文字列として取得している。
            if (!is_null($id)) {
                //existにしている理由はidが存在している場合はtrueが帰ってくる
                $itemId = Product::availableItems()->where('products.id', $id)->exists();
                //itemIdが存在しているかどうかで判定を行っている。
                if (!$itemId) {
                    abort(404);
                }
            }
            return $next($request);
        });
    }

    public function index(Request $request){


        //メール送信を同期的に行い、return viewの処理が行われる前にメール送信を行う処理
        // Mail::to('test@example.com')
        //     ->send(new TestMail());

        //非同期処理でメール送信を行う。viewに戻る処理を行う前に実行する機能
        // SendThanksMail::dispatch();

        $categories = PrimaryCategory::with('secondary')->get();

        $products = Product::availableItems()
        ->selectCategory($request->category ?? '0')
        ->sortOrder($request->sort)
        ->paginate($request->pagination ?? '20');

        return view('user.index' , compact('products', 'categories'));
    }

    public function show($id){

        $product = Product::findOrFail($id);
        $quantity = Stock::where('product_id', $product->id)
            ->sum('quantity');
        if($quantity > 9){
            $quantity = 9;
        }

        return view('user.show', compact('product', 'quantity'));
    }

}
