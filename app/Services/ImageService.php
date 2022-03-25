<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ImageService{
    public static function upload($imageFile, $folderName){
        // Storage::putFile('public/shops', $imageFile); リサイズなしであればputFileメソッドで実行できる
        //Interventionを使用するとfileをファイルから画像に変換してしまうので、putFileが適応できなくなってしまう。

        $fileName = uniqid(rand() . '_');
        $extension = $imageFile->extension();
        $fileNameToStore = $fileName . '.' . $extension;

        //画像をリサイズする(InterventionImageは正しく動いているので、赤線が出ていて問題ないです。)
        $resizedImage = InterventionImage::make($imageFile)->resize(1920, 1080)->encode();

        Storage::put('public/' . $folderName . '/' . $fileNameToStore, $resizedImage);
        return $fileNameToStore;
    }
}
