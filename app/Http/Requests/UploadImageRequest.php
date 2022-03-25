<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    /**
     * 認証されているユーザーが使えるかどうか、基本的にはtrue
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //ルール＝imageであるかどうか、拡張子はjpgかjpegかpngか最大の大きさは2MBまで
            'image' => 'image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(){
        return [
            'image' => '指定されたファイルが画像ではありません。',
            'mines' => '指定された拡張子(jpg/jpeg/png)ではありません。',
            'max' => 'ファイルサイズは2MB以下にしてください。'
        ];
    }
}
