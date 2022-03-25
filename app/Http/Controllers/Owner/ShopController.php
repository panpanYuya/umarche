<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ShopController extends Controller
{
    //
    public function __construct(){
        $this->middleware('auth:owners');

        $this->middleware(function ($request, $next){
            //parametersにすると配列で取得してしまうみたい
            $id = $request->route()->parameter("shop");//文字列として取得している。
            if(!is_null($id)){
                $shopOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int)$shopOwnerId;
                $userId = Auth::id();//数字として取得している

                //現在ログインしているユーザーのidと一致しているかを確認。
                if($userId !== $shopId){
                    abort(404);
                }
            }
            return $next($request);

        });
    }

    public function index(){
        $ownerId = Auth::id();
        $shops = Shop::where('owner_id', $ownerId)->get();
        return view('owner.shops.index', compact('shops'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shop = Shop::findOrFail($id);
        return view('owner.shops.edit', compact('shop'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

        $imageFile = $request->image;
        //isValidはアップロードができているかを確認している
        if(!is_null($imageFile) && $imageFile->isValid()){
            // Storage::putFile('public/shops', $imageFile); リサイズなしであればputFileメソッドで実行できる
            //Interventionを使用するとfileをファイルから画像に変換してしまうので、putFileが適応できなくなってしまう。

            $fileName = uniqid(rand().'_');
            $extension = $imageFile->extension();
            $fileNameToStore = $fileName. '.' .$extension;

            //画像をリサイズする(InterventionImageは正しく動いているので、赤線が出ていて問題ないです。)
            $resizedImage = InterventionImage::make($imageFile)->resize(1920, 1080)->encode();

            Storage::put('public/shops/' . $fileNameToStore, $resizedImage);
        }

        return redirect()->route('owner.shops.index');
    }
}
