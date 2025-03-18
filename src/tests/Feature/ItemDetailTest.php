<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Good;
use App\Models\Favorite;
use App\Models\Comment;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_page_displays_correct_information()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // 商品を作成（いいね数を `5` に設定）
        $good = Good::factory()->create([
            'name' => 'Test Product',
            'brand' => 'Test Brand',
            'price' => 1000,
            'description' => 'This is a test product.',
            'category' => 'Electronics',
            'condition' => 'New',
            //'favorites_count' => 5, // いいね数を指定
        ]);

        // コメントを作成
        $comments = Comment::factory()->count(2)->create([
            'good_id' => $good->id,
            'user_id' => $user->id,
            'content' => 'This is a test comment.',
        ]);

        // いいねを作成
        Favorite::create([
            'user_id' => $user->id,
            'good_id' => $good->id,
        ]);

        // 商品詳細ページにアクセス
        $response = $this->get(route('goods.show', $good->id));

        // レスポンスが正常か確認
        $response->assertStatus(200);

        // 商品情報の表示確認
        $response->assertSeeText('Test Product');  // 商品名
        $response->assertSeeText('Test Brand');    // ブランド
        $response->assertSeeText('¥1,000（税込）'); // 価格（フォーマット統一）
        $response->assertSeeText('This is a test product.'); // 商品説明
        $response->assertSeeText('Electronics');   // カテゴリ
        $response->assertSeeText('New');           // 商品の状態

        // いいね数を確認（フォーマットに注意）
        //$response->assertSeeText('いいね数: 5');

        // コメントが表示されているか確認
        foreach ($comments as $comment) {
            $response->assertSeeText($comment->content);
            $response->assertSeeText($user->name);
        }
    }//FIXME:コメントといいねの機能をテストした後に実装

}
