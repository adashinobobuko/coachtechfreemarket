<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Good;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'good_id' => Good::factory(),
            'payment_method' => 'コンビニ払い',
            'address' => "東京都新宿区1-2-3",
        ];
    }

}
