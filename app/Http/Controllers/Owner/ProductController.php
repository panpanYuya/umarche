<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Image;
use App\Models\Product;
use App\Models\PrimaryCategory;
use App\Models\Owner;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:owners');

        $this->middleware(function ($request, $next) {
            //parametersにすると配列で取得してしまうみたい
            $id = $request->route()->parameter("product"); //文字列として取得している。
            if (!is_null($id)) {
                $productOwnerId = Product::findOrFail($id)->shop->owner->id;
                $productId = (int)$productOwnerId; //数字として取得している

                //現在ログインしているユーザーのidと一致しているかを確認。
                if ($productId !== Auth::id()) {
                    abort(404);
                }
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //下記のコードはN+1問題に抵触する分の為コメントアウト
        // $products = Owner::findOrFail(Auth::id())->shop->product;

        //withのなかはphpで書き直すとshop->product->imageFirstまでを取ってくると書いている。
        $ownerInfo = Owner::with('shop.product.imageFirst')->where('id', Auth::id())->get();

        return view('owner.products.index', compact('ownerInfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shops = Shop::where('owner_id', Auth::id())
        ->select('id', 'name')
        ->get();

        $images = Image::where('owner_id', Auth::id())
        ->select('id', 'title', 'filename')
        ->orderby('updated_at', 'desc')
        ->get();

        $categories = PrimaryCategory::with('secondary')
        ->get();

        return view('owner.products.create', compact('shops', 'images', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'information' => ['required', 'string','max:1000'],
            'price' => ['required', 'integer'],
            'sort_order' => ['nullable', 'integer'],
            'quantity' => ['required', 'integer'],
            'shop_id' => ['required', 'exists:shops,id'],
            'category' => ['required', 'exists:secondary_categories,id'],
            'image1' => ['nullable', 'exists:images,id'],
            'image2' => ['nullable', 'exists:images,id'],
            'image3' => ['nullable', 'exists:images,id'],
            'image4' => ['nullable', 'exists:images,id'],
            'is_selling' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {

                //オーナー登録
                $product = Product::create([
                    'name' => $request->name,
                    'information' => $request->information,
                    'price' => $request->price,
                    'sort_order' => $request->sort_order,
                    'shop_id' => $request->shop_id,
                    'secondary_category_id' => $request->category,
                    'image1' => $request->image1,
                    'image2' => $request->image1,
                    'image3' => $request->image1,
                    'image4' => $request->image1,
                    'is_selling' => $request->is_selling,
                ]);

                //店登録
                Stock::create([
                    'product_id' => $product->id,
                    'type' => 1,
                    'quantity' => $request->quantity,
                ]);

                $product->id;
            }, 2);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }


        return redirect()
        ->route('owner.products.index')
        ->with([
            'message' => '商品登録を行いました。',
            'status' => 'info',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
