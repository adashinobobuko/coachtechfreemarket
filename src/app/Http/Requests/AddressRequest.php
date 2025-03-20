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
            //
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10|regex:/^\d{3}-\d{4}$/',
            'address' => 'nullable|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ];
        
    }

    /**
     * カスタムエラーメッセージ
     */
    public function messages()
    {
        return [
            'name.required' => '名前を入力してください。',
            'postal_code.string' => '郵便番号は文字列である必要があります。',
            'address.string' => '住所は文字列である必要があります。',
            'building_name' => '建物名は文字列である必要があります。',
        ];
    }
}
