<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Good;
use App\Models\PurchasesAddress;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class AddressChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // データベースリセット
        Config::set('database.connections.mysql.database', 'demo_test');
        DB::purge('mysql');
        $this->artisan('migrate');

        // ユーザーが存在する場合は削除
        User::where('email', 'testuser@example.com')->delete();

        session(['errors' => new \Illuminate\Support\MessageBag()]);

        $this->withoutMiddleware();

        // セッションを強制的に開始
        Session::start();
        $this->withSession([]); 
    }

    //12
    public function test_address_change_reflects_on_purchase_screen()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 商品を作成
        $good = Good::factory()->create([
            'name' => 'Test Product',
            'price' => 5000,
            'image' => 'test-product.jpg',
        ]);

        // 住所変更フォームに新しい住所を登録
        $newAddressData = [
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区1-2-3',
            'building_name' => '新しいビル202号室',
        ];

        // 住所変更リクエスト送信
        $response = $this->post(route('address.change.update', ['goodsid' => $good->id]), $newAddressData);
        $response->assertRedirect(route('buy.show', ['id' => $good->id])); // 住所変更後に購入画面へリダイレクト

        // DBに変更後の住所が保存されているか確認
        $this->assertDatabaseHas('purchases_addresses', [
            'good_id' => $good->id,
            'postal_code' => $newAddressData['postal_code'],
            'address' => $newAddressData['address'],
            'building_name' => $newAddressData['building_name'],
        ]);

        // 商品購入画面を再度開く
        $purchaseScreenResponse = $this->get(route('buy.show', ['id' => $good->id]));

        // 変更後の住所が表示されているか確認
        $purchaseScreenResponse->assertSee('987-6543');
        $purchaseScreenResponse->assertSee('東京都渋谷区1-2-3');
        $purchaseScreenResponse->assertSee('新しいビル202号室');
    }

    public function test_purchase_registers_with_updated_address()//FIXMEなぜか購入商品が登録できない
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 商品を作成
        $good = Good::factory()->create([
            'name' => 'Test Product',
            'price' => 5000,
            'image' => 'test-product.jpg',
        ]);

        // 住所変更フォームに新しい住所を登録
        $newAddressData = [
            'postal_code' => '987-6543',
            'address' => '東京都渋谷区1-2-3',
            'building_name' => '新しいビル202号室',
        ];

        // 住所変更リクエスト送信
        $response = $this->post(route('address.change.update', ['goodsid' => $good->id]), $newAddressData);
        $response->assertRedirect(route('buy.show', ['id' => $good->id])); // 住所変更後に購入画面へリダイレクト

        // DBに変更後の住所が保存されているか確認
        $this->assertDatabaseHas('purchases_addresses', [
            'good_id' => $good->id,
            'postal_code' => $newAddressData['postal_code'],
            'address' => $newAddressData['address'],
            'building_name' => $newAddressData['building_name'],
        ]);

        // 商品購入画面を再度開く
        $purchaseScreenResponse = $this->get(route('buy.show', ['id' => $good->id]));

        // 変更後の住所が表示されているか確認
        $purchaseScreenResponse->assertSee('987-6543');
        $purchaseScreenResponse->assertSee('東京都渋谷区1-2-3');
        $purchaseScreenResponse->assertSee('新しいビル202号室');

        // 購入処理（コンビニ払い）
        $purchaseResponse = $this->post(route('purchase.store', ['id' => $good->id]), [
            'good_id' => $good->id,
            'payment_method' => 'コンビニ払い',
        ]);

        $purchases = DB::table('purchases')->get();
        dd($purchases);
        
        // 購入情報がDBに保存されたことを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'good_id' => $good->id,
            'payment_method' => 'コンビニ払い',
        ]);

        // 購入した商品に新しい住所が紐づいているか確認
        $this->assertDatabaseHas('purchases_addresses', [
            'good_id' => $good->id,
            'postal_code' => $newAddressData['postal_code'],
            'address' => $newAddressData['address'],
            'building_name' => $newAddressData['building_name'],
        ]);
        
    }
}
