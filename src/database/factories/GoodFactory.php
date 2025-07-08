<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Good;
use App\Models\User;

class GoodFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(), // ランダムなユーザーを生成
            'image' => $this->faker->imageUrl(), // ダミー画像URL
            'brand' => $this->faker->company(),
            'condition' => $this->faker->randomElement(['new', 'used', 'refurbished']),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 50000),
            'is_sold' => false,
            'favorites_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
