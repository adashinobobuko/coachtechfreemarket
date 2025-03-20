<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Good;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;
    //11
    public function test_payment_method_selection_updates_display()
    {
        // ユーザー作成
        $user = User::factory()->create();
        $this->actingAs($user);

        // 商品作成
        $good = Good::factory()->create([
            'name' => 'Test Product',
            'price' => 1000,
            'image' => 'test-image.jpg',
        ]);

        // 支払い方法選択画面を開く
        $response = $this->get(route('buy.show', ['id' => $good->id]));
        $response->assertStatus(200);

        // 初期状態の支払い方法の表示を確認
        $response->assertSeeText('支払');

        // **支払い方法を変更**
        $response = $this->get(route('buy.show', ['id' => $good->id, 'payment_method' => 'コンビニ払い']));
        $response->assertStatus(200);
        $response->assertSeeText('コンビニ払い');

        $response = $this->get(route('buy.show', ['id' => $good->id, 'payment_method' => 'カード払い']));
        $response->assertStatus(200);
        $response->assertSeeText('カード払い');
    }

}
