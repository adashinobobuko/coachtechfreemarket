<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class Purchaserequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check(); // ユーザーがログインしている場合のみ許可
    }

    public function rules()
    {
        return [
            'payment_method' => 'required|in:コンビニ払い,カード払い',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();

            if (!$user->address) {
                $validator->errors()->add('address', '住所を登録してください。');
            }
        });
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in' => '支払い方法はコンビニ払いまたはカード払いを選択してください。',
        ];
    }
}
