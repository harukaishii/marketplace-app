<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PaymentType;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
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
            'payment_type' =>[
                'required',
                Rule::in(array_column(PaymentType::cases(),'value')),
            ],
            'user_address_id' =>  ['required']
        ];
    }

    public function messages()
    {
        return [
            'payment_type.required' => '支払い方法を選択してください',
            'user_address_id.required' =>'送付先の住所が登録されていません'
        ];
    }
}


