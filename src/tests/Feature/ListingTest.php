<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Good;

class ListingTest extends TestCase
{
    use RefreshDatabase; 
    //15
    public function test_it_displays_sell_form_and_stores_good_successfully()
    {
        Storage::fake('public'); // ストレージをフェイク化

        // ユーザー作成 & ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. `showSellForm` のテスト (出品フォームが表示されるか)
        $response = $this->get(route('sellform')); 
        $response->assertStatus(200);
        $response->assertViewIs('listing');

        // 2. `store` のテスト (商品を登録できるか)
        $image = UploadedFile::fake()->createWithContent('listing.jpg', 'fake_image_content'); // テスト用のダミー画像

        $data = [
            'image' => $image,
            'category' => ['カテゴリ1', 'カテゴリ2'],
            'condition' => '新品',
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト用の商品です',
            'price' => 5000,
        ];

        $response = $this->post(route('sellform.store'), $data);

        // 成功時のリダイレクト & メッセージ確認
        $response->assertRedirect(route('index'));
        $response->assertSessionHas('success', '商品が出品されました');

        // データベースに商品が保存されているか確認
        $this->assertDatabaseHas('goods', [
            'user_id' => $user->id,
            'category' => 'カテゴリ1,カテゴリ2',
            'condition' => '新品',
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト用の商品です',
            'price' => 5000,
        ]);

        // ストレージに画像が保存されているか確認
        Storage::disk('public')->assertExists('goods/' . $image->hashName());
    }

}

