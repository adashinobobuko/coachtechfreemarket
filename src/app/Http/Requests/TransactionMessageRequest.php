<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionMessageRequest extends FormRequest
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

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:400'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => '本文を入力してください',
            'message.max' => 'メッセージは400文字以内で入力してください',
            'image.mimes' => '「.png」または「.jpg」でアップロードしてください',
        ];
    }
}
