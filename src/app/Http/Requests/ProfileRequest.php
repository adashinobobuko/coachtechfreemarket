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
            'profile_image' => 'nullable|image|max:1024|mimes:jpeg,png', // 画像は最大1MB
        ];
    }

    public function messages()
    {
        return [
            'profile_image.mimes' => '画像は jpeg, png, jpg のいずれかの形式でアップロードしてください。',
            'profile_image.max' => '画像のサイズは1MB以下にしてください。',
        ];
    }
}
