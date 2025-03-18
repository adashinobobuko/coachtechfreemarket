<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Good;
use App\Models\Favorite;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_goods_by_keyword()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // いくつかの商品を作成
        $matchingGood = Good::factory()->create(['name' => 'Special Item']);
        $nonMatchingGood = Good::factory()->create(['name' => 'Regular Product']);

        // 検索リクエストを送信（"Special" で検索）
        $response = $this->get(route('search', ['keyword' => 'Special']));

        // レスポンスが正常か確認
        $response->assertStatus(200);

        // 検索結果に "Special Item" が含まれているか
        $response->assertSeeText('Special Item');
        $response->assertDontSeeText('Regular Product');
    }

    public function test_user_can_search_for_products_add_to_mylist_and_navigate()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 商品を作成
        $matchingGood = Good::factory()->create(['name' => 'Special Product']);
        $otherGood = Good::factory()->create(['name' => 'Random Item']);

        // ホームページで検索（"Special" で検索）
        $response = $this->get(route('search', ['keyword' => 'Special']));

        // 検索結果ページが正常に開くか確認
        $response->assertStatus(200);

        // 検索結果が正しく表示されているか
        $response->assertSeeText('Special Product'); // ✅ 一致する商品が表示される
        $response->assertDontSeeText('Random Item'); // ✅ 検索に合わない商品は表示されない

        // ✅ Special Product を「いいね」してマイリストに追加
        Favorite::create([
            'user_id' => $user->id,
            'good_id' => $matchingGood->id,
        ]);

        // マイリストページへアクセス
        $response = $this->get(route('mylist'));

        // マイリストページが正常に開くか確認
        $response->assertStatus(200);

        // ✅ マイリストに「Special Product」が表示されることを確認
        $response->assertSeeText('Special Product');
    }

}
