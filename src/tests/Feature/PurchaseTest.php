<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Good;
use App\Models\Purchase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_button_buy_completed()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区1-2-3'
        ]);
        $this->actingAs($user);

        //商品のデータを作成
        $good = Good::factory()->create([
            'name' => 'Test Product',
            'price' => 1000,
            'image' => 'test-image.jpg',
            'is_sold' => false, //未購入
        ]);

        //購入画面を開く
        $response = $this->get(route('buy.show',['id' => $good->id]));

        // レスポンスが正常か確認
        $response->assertStatus(200);

        //商品購入ページの表示を確認
        $response->assertSeeText('Test Product');  // 商品名
        $response->assertSeeText('¥1,000（税込）'); // 価格
        $response->assertSeeText('東京都新宿区1-2-3'); // 住所確認        
        $response->assertSeeText('コンビニ払い') ;//支払方法

        //購入方法（この際コンビニ払い）
        $purchaseResponse = $this->post(route('purchase.store',['id' => $good->id]),[
            'good_id' => $good->id,
            'payment_method' => 'コンビニ払い',
        ]);

        //購入後、ホームページへリダイレクト
        $purchaseResponse->assertRedirect(route('index'));

        //購入完了がしましたが表示される
        $this->followRedirects($purchaseResponse)->assertSeeText('購入が完了しました');
    }

    public function test_solddisplay_after_purchase_button_buy_completed()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区1-2-3'
        ]);
        $this->actingAs($user);

        // 商品のデータを作成
        $good = Good::factory()->create([
            'name' => 'Test Product',
            'price' => 1000,
            'image' => 'test-image.jpg',
            'is_sold' => false, // 未購入
        ]);

        // 購入画面を開く
        $response = $this->get(route('buy.show', ['id' => $good->id]));
        $response->assertStatus(200);

        // 商品購入ページの表示を確認
        $response->assertSeeText('Test Product');  // 商品名
        $response->assertSeeText('¥1,000（税込）'); // 価格
        $response->assertSeeText('東京都新宿区1-2-3'); // 住所確認        
        $response->assertSeeText('コンビニ払い'); // 支払方法

        // 購入処理（コンビニ払い）
        $purchaseResponse = $this->post(route('purchase.store', ['id' => $good->id]), [
            'good_id' => $good->id,
            'payment_method' => 'コンビニ払い',
        ]);

        // 購入後、ホームページへリダイレクト
        $purchaseResponse->assertRedirect(route('index'));

        // 購入完了メッセージが表示されることを確認
        $this->followRedirects($purchaseResponse)->assertSeeText('購入が完了しました');

        // 商品が「売り切れ」ステータスになっていることを確認
        $this->assertDatabaseHas('goods', [
            'id' => $good->id,
            'is_sold' => true,
        ]);

        // 最新の状態を取得
        $good->refresh();

        // トップページにアクセス
        $homeResponse = $this->get(route('index'));
        $homeResponse->assertStatus(200);

        // 売り切れ表示の確認
        $homeResponse->assertSeeText('sold');
    }

    public function test_mypage_display_purchase_button_buy_completed()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都新宿区1-2-3'
        ]);
        $this->actingAs($user);

        //商品のデータを作成
        $good = Good::factory()->create([
            'name' => 'Test Product',
            'price' => 1000,
            'image' => 'test-image.jpg',
            'is_sold' => false, //未購入
        ]);

        //購入画面を開く
        $response = $this->get(route('buy.show',['id' => $good->id]));

        // レスポンスが正常か確認
        $response->assertStatus(200);

        //商品購入ページの表示を確認
        $response->assertSeeText('Test Product');  // 商品名
        $response->assertSeeText('¥1,000（税込）'); // 価格
        $response->assertSeeText('東京都新宿区1-2-3'); // 住所確認        
        $response->assertSeeText('コンビニ払い') ;//支払方法

        //購入方法（この際コンビニ払い）
        $purchaseResponse = $this->post(route('purchase.store',['id' => $good->id]),[
            'good_id' => $good->id,
            'payment_method' => 'コンビニ払い',
        ]);

        //購入後、ホームページへリダイレクト
        $purchaseResponse->assertRedirect(route('index'));

        //購入完了がしましたが表示される
        $this->followRedirects($purchaseResponse)->assertSeeText('購入が完了しました');

        // 商品が「売り切れ」ステータスになっていることを確認
        $this->assertDatabaseHas('goods', [
            'id' => $good->id,
            'is_sold' => true,
        ]);

        // 最新の状態を取得
        $good->refresh();

        // マイページ「購入した商品」タブにアクセス
        $mypageResponse = $this->get(route('mypage.buy'));
        $mypageResponse->assertStatus(200);

        // 買った商品が表示されているかの確認
        $mypageResponse->assertSeeText('Test Product'); // 商品名
        $mypageResponse->assertSee('test-image.jpg'); // 商品画像
    }

    

}
