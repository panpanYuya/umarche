<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Shop;
use App\Models\SecondaryCategory;
use App\Models\Image;
use App\Models\Stock;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'information',
        'price',
        'is_selling',
        'sort_order',
        'secondary_category_id',
        'image1',
        'image2',
        'image3',
        'image4',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        //method名をsecondaryCategoryにしていないので、第二引数にリレーション先のカラムを追加する必要がある。
        return $this->belongsTo(SecondaryCategory::class, 'secondary_category_id');
    }

    //image1にするとリレーション先のカラム名と一致してしまい、エラーが発生してしまうので、名前を変更。(image1はproductsテーブルのカラムに存在している。)
    public function imageFirst()
    {
        //リレーション先はImageモデル
        return $this->belongsTo(Image::class, 'image1', 'id');
    }

    public function imageSecond()
    {
        //リレーション先はImageモデル
        return $this->belongsTo(Image::class, 'image2', 'id');
    }
    public function imageThird()
    {
        //リレーション先はImageモデル
        return $this->belongsTo(Image::class, 'image3', 'id');
    }
    public function imageFourth()
    {
        //リレーション先はImageモデル
        return $this->belongsTo(Image::class, 'image4', 'id');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function users(){
        //withPivotで中間テーブルのquantityを取得できている。
        return $this->belongsToMany(User::class,'carts')->withPivot(['id', 'quantity']);
    }

    public function scopeAvailableItems($query){
        $stocks = DB::table('t_stocks')
            ->select('product_id', DB::raw('sum(quantity) as quantity'))
            ->groupBy('product_id')
            ->having('quantity', '>', 1);


        return $query->joinSub($stocks, 'stock', function ($join) {
            $join->on('products.id', '=', 'stock.product_id');
        })
        ->join('shops', 'products.shop_id', '=', 'shops.id')
        ->join('secondary_categories', 'products.secondary_category_id', '=', 'secondary_categories.id')
        ->join('images as image1', 'products.image1', '=', 'image1.id')
        ->where('shops.is_selling', true)
        ->where('products.is_selling', true)
        ->select(
            'products.id as id',
            'products.name as name',
            'products.price',
            'products.sort_order as sort_order',
            'products.information',
            'secondary_categories.name as category',
            'image1.filename as filename'
        );

    }

}
