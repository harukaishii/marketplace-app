<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'post' => 'required|regex:/^\d{3}-\d{4}$/|max:8',
            'address' => 'required',
            'building' => 'nullable',
        ];

        if (!$this->routeIs('purchase.updateAddress')) {
            $rules['name'] = 'required';
        }
    }

    public function messages()
    {
        return [
            'name.required' =>'お名前を入力してください',
            'post.required' => '郵便番号を入力してください',
            'post.max'=> '郵便番号は8文字以内で入力してください',
            'post.regex' => '郵便番号はハイフンありの「123-4567」の形式で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
