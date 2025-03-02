<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void 
    {
        DB::table('goods')->insert([
            [
                'user_id' => 1,
                'name' => '腕時計',
                'price' => 15000,
                'category' => 'ファッション,メンズ,アクセサリー',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'testitem/Armani+Mens+Clock.jpg', 
                'condition' => '良好'
            ],
            [
                'user_id' => 1,
                'name' => 'HDD',
                'price' => 5000,
                'category' => '家電',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'testitem/HDD+Hard+Disk.jpg', 
                'condition' => '目立った傷や汚れなし'
            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'category' => 'キッチン',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'testitem/iLoveIMG+d.jpg', 
                'condition' => 'やや傷や汚れあり'
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'price' => 4000,
                'category' => 'ファッション,メンズ',
                'description' => 'クラシックなデザインの革靴',
                'image' => 'testitem/Leather+Shoes+Product+Photo.jpg', 
                'condition' => '状態が悪い'
            ],
            [
                'user_id' => 1,
                'name' => 'ノートPC',
                'price' => 45000,
                'category' => '家電',
                'description' => '高性能なノートパソコン',
                'image' => 'testitem/Living+Room+Laptop.jpg', 
                'condition' => '良好'
            ],
            [
                'user_id' => 2,
                'name' => 'マイク',
                'price' => 8000,
                'category' => '家電,アクセサリー',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'testitem/Music+Mic+4632231.jpg', 
                'condition' => '目立った傷や汚れなし'
            ],
            [
                'user_id' => 2,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'category' => 'ファッション,レディース,アクセサリー',
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'testitem/Purse+fashion+pocket.jpg', 
                'condition' => 'やや傷や汚れあり'
            ],
            [
                'user_id' => 2,
                'name' => 'タンブラー',
                'price' => 500,
                'category' => 'キッチン',
                'description' => '使いやすいタンブラー',
                'image' => 'testitem/Tumbler+souvenir.jpg', 
                'condition' => '状態が悪い'
            ],
            [
                'user_id' => 2,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'category' => 'キッチン',
                'description' => '手動のコーヒーミル',
                'image' => 'testitem/Waitress+with+Coffee+Grinder.jpg', 
                'condition' => '良好'
            ],
            [
                'user_id' => 2,
                'name' => 'メイクセット',
                'price' => 2500,
                'category' => 'ファッション,レディース',
                'description' => '便利なメイクアップセット',
                'image' => 'testitem/外出メイクアップセット.jpg', 
                'condition' => '目立った傷や汚れなし'
            ]
        ]);
    }
}
