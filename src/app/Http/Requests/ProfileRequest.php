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
            'profile_image' => 'nullable|image|max:2048|mimes:jpeg,png', // 画像は最大2MB
            //'postal_code' => 'nullable|string|max:10',
            //'address' => 'nullable|string|max:255',
            //'building_name' => 'nullable|string|max:255',
        ];
    }
}
