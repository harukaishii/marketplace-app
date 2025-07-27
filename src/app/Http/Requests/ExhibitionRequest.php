<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ItemCondition;

class ExhibitionRequest extends FormRequest
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
        $conditionValues = implode(',', array_column(ItemCondition::cases(), 'value'));

        return [
            'name' => 'required',
            'detail' => 'required|string',
            'brand_name' => 'nullable|string|max:255',
            'price' => 'required|integer|min:0',
            'condition' => 'required|in:' . $conditionValues,
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'product_image' => 'required|image',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'detail.required' => '商品説明を入力してください',
            'product_image.required' => '画像を選択してください',
            'product_image.image' =>'画像はjpegかpng形式を選択してください',
            'category_ids' => 'カテゴリーを選択してください',
            'condition' => '商品の状態を選択してください',
            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は数字で入力してください',
            'price.min'=> '商品価格は0以上の数字で入力してください'
        ];
    }
}
