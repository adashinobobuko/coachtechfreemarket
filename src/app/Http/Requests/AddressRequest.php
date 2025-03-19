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
            'profile_image' => 'nullable|image|max:2048|mimes:jpeg,png', // 画像は最大2MB
            'postal_code' => 'nullable|string|max:10|regex:/^\d{3}-\d{4}$/',
            'address' => 'nullable|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ];
        
    }
}
