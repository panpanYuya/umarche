<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\SecondaryCategory;
use App\Models\Image;

class Product extends Model
{
    use HasFactory;

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

}
