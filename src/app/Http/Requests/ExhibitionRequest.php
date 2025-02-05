<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category' => 'required|array', // 配列として受け取る
            'condition' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:1'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'image.required' => '画像を選択してください。',
            'image.image' => '画像ファイルをアップロードしてください。',
            'image.mimes' => '画像は jpeg, png, jpg のいずれかの形式でアップロードしてください。',
            'image.max' => '画像のサイズは2MB以下にしてください。',
            'category.required' => 'カテゴリを入力してください。',
            'category.string' => 'カテゴリは文字列で入力してください。',
            'condition.required' => '商品の状態を入力してください。',
            'condition.string' => '商品の状態は文字列で入力してください。',
            'name.required' => '商品名を入力してください。',
            'name.string' => '商品名は文字列で入力してください。',
            'description.required' => '商品の説明を入力してください。',
            'description.string' => '商品の説明は文字列で入力してください。',
            'description.max' => '商品の説明は255文字以内で入力してください。',
            'price.required' => '価格を入力してください。',
            'price.numeric' => '価格は数値で入力してください。',
            'price.min' => '価格は1円以上にしてください。',
        ];
    }
    
}
