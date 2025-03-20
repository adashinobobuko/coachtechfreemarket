<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Good;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ListingTest extends TestCase
{
    use RefreshDatabase; 

    protected function setUp(): void
    {
        parent::setUp();

        // データベースリセット
        \Config::set('database.connections.mysql.database', 'demo_test');
        \DB::purge('mysql'); // `DB` もグローバル参照にする
        $this->artisan('migrate');

        session(['errors' => new \Illuminate\Support\MessageBag()]);

        $this->withoutMiddleware();
    }

    //15
    public function test_it_displays_sell_form_and_stores_good_successfully()
    {
        Storage::fake('public'); // ストレージをフェイク化

        $user = User::factory()->create(); // ユーザー作成
        $response = $this->actingAs($user)
        ->withSession(['errors' => new \Illuminate\Support\MessageBag()])
        ->get(route('sellform'));

        // 2. `store` のテスト (商品を登録できるか)
        $image = UploadedFile::fake()->create('listing.jpg', 100); // テスト用のダミー画像

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
        $response->assertSessionHas('success');

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

