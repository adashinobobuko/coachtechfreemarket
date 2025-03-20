<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\User;
use App\Models\Good;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // ユーザーのIDを取得
            'good_id' => Good::factory(), // 商品のIDを取得
        ];
    }
}
