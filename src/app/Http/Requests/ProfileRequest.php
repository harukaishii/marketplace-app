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
            'image_file' => 'required|mimes:jpeg,png'
        ];
    }

    public function messages()
    {
        return [
            'image_file.required' => '画像ファイルを選択してください',
            'image_file.mimes' => '画像は.jpegまたは.png形式で指定してください',
        ];
    }
}
