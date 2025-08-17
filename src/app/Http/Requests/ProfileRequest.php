<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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

            'image_file' => [
                'sometimes', // リクエストにimage_fileがある場合のみバリデーションを適用
                'image',
                'mimes:jpeg,png',
                'max:2048',
                // 既存の画像がなく、かつimage_fileが送信されていない場合にrequiredを適用するルール
                // 'current_image_exists'という隠しフィールド
                'required_without:current_image_exists',
            ],
        ];
    }

    public function messages()
    {
        return [
            'image_file.required_without' => '画像ファイルを選択してください',
            'image_file.mimes' => '画像ファイルは.jpegまたは.png形式で指定してください',
            'image_file.max' => '画像ファイルのサイズは2MBを超えないでください',
        ];
    }
}
