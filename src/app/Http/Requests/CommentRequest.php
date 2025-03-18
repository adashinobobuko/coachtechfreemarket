<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
            'content' => 'required|string|max:255',
        ];
    }

        /**
     * カスタムエラーメッセージ
     */
    public function messages()
    {
        return [
            'content.required' => 'コメントを入力してください。',
            'content.string' => 'コメントは文字列である必要があります。',
            'content.max' => 'コメントは255文字以内で入力してください。',
        ];
    }
}
