<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

}
