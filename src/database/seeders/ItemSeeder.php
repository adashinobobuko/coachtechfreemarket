<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // ユーザー1と2を作成（存在チェックあり）
        $users = [
            ['id' => 1, 'name' => 'User One', 'email' => 'user1@example.com'],
            ['id' => 2, 'name' => 'User Two', 'email' => 'user2@example.com'],
            ['id' => 3, 'name' => 'User Three', 'email' => 'user3@example.com'], // 紐づかないユーザー
        ];
        
        foreach ($users as $user) {
            if (!DB::table('users')->where('id', $user['id'])->exists()) {
                DB::table('users')->insert([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'profile_image' => 'test/profile' . $user['id'] . '.jpg',       // storage配下に仮画像を用意しておく
                    'postal_code' => '123-4567',
                    'address' => '東京都渋谷区1-2-3',
                    'building_name' => 'コーポA-101',
                    'profile_completed' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // カテゴリ名 → ID のマップを作成
        $categoryMap = DB::table('categories')->pluck('id', 'name');

        // 商品データ定義
        $goodsData = [
            [
                'user_id'    => 1,
                'name'       => '腕時計',
                'price'      => 12000,
                'category'   => 'ファッション,メンズ,アクセサリー',
                'description'=> 'スタイリッシュなアナログ腕時計',
                'image'      => 'images/testitem/Armani+Mens+Clock.jpg',
                'condition'  => '良好',
            ],
            [
                'user_id'    => 1,
                'name'       => 'HDD',
                'price'      => 5000,
                'category'   => '家電',
                'description'=> '高速で信頼性の高いハードディスク',
                'image'      => 'images/testitem/HDD+Hard+Disk.jpg',
                'condition'  => '目立った傷や汚れなし',
            ],
            [
                'user_id'    => 1,
                'name'       => '玉ねぎ3束',
                'price'      => 300,
                'category'   => 'キッチン',
                'description'=> '新鮮な玉ねぎ3束のセット',
                'image'      => 'images/testitem/iLoveIMG+d.jpg',
                'condition'  => 'やや傷や汚れあり',
            ],
            [
                'user_id'    => 1,
                'name'       => '革靴',
                'price'      => 4000,
                'category'   => 'ファッション,メンズ',
                'description'=> 'クラシックなデザインの革靴',
                'image'      => 'images/testitem/Leather+Shoes+Product+Photo.jpg',
                'condition'  => '状態が悪い',
            ],
            [
                'user_id'    => 1,
                'name'       => 'ノートPC',
                'price'      => 45000,
                'category'   => '家電',
                'description'=> '高性能なノートパソコン',
                'image'      => 'images/testitem/Living+Room+Laptop.jpg',
                'condition'  => '良好',
            ],
            [
                'user_id'    => 2,
                'name'       => 'マイク',
                'price'      => 8000,
                'category'   => '家電,アクセサリー',
                'description'=> '高音質のレコーディング用マイク',
                'image'      => 'images/testitem/Music+Mic+4632231.jpg',
                'condition'  => '目立った傷や汚れなし',
            ],
            [
                'user_id'    => 2,
                'name'       => 'ショルダーバッグ',
                'price'      => 3500,
                'category'   => 'ファッション,レディース,アクセサリー',
                'description'=> 'おしゃれなショルダーバッグ',
                'image'      => 'images/testitem/Purse+fashion+pocket.jpg',
                'condition'  => 'やや傷や汚れあり',
            ],
            [
                'user_id'    => 2,
                'name'       => 'タンブラー',
                'price'      => 500,
                'category'   => 'キッチン',
                'description'=> '使いやすいタンブラー',
                'image'      => 'images/testitem/Tumbler+souvenir.jpg',
                'condition'  => '状態が悪い',
            ],
            [
                'user_id'    => 2,
                'name'       => 'コーヒーミル',
                'price'      => 4000,
                'category'   => 'キッチン',
                'description'=> '手動のコーヒーミル',
                'image'      => 'images/testitem/Waitress+with+Coffee+Grinder.jpg',
                'condition'  => '良好',
            ],
            [
                'user_id'    => 2,
                'name'       => 'メイクセット',
                'price'      => 2500,
                'category'   => 'ファッション,レディース',
                'description'=> '便利なメイクアップセット',
                'image'      => 'images/testitem/外出メイクアップセット.jpg',
                'condition'  => '目立った傷や汚れなし',
            ],
        ];
        
        // 商品と中間テーブルを登録
        foreach ($goodsData as $data) {
            $categoryNames = explode(',', $data['category']);
            $categoryIds = collect($categoryNames)
                ->map(fn($name) => trim($categoryMap[$name] ?? null))
                ->filter()
                ->all();

            // categoryキーはDBに不要なので除去
            $goodId = DB::table('goods')->insertGetId(Arr::except($data, ['category']));

            foreach ($categoryIds as $categoryId) {
                DB::table('category_good')->insert([
                    'good_id'    => $goodId,
                    'category_id'=> $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
