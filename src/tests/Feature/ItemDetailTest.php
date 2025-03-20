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
    //7
    public function test_product_detail_page_displays_correct_information()
    {
        $user = User::factory()->create();

        // 商品データを作成
        $product = Good::factory()->create([
            'name' => 'Test Product',
            'brand' => 'Test Brand',
            'price' => 1000,
            'description' => 'This is a test product.',
            'category' => 'Electronics',
            'condition' => 'New',
            'favorites_count' => 5, // 明示的に設定
        ]);

        // コメントデータを追加
        Comment::factory()->count(3)->create([
            'user_id' => $user->id,
            'good_id' => $product->id,
            'content' => 'This is a test comment.',
        ]);

        // コメントを取得
        $comments = Comment::where('good_id', $product->id)->get();

        // いいねデータを追加
        Favorite::factory()->count(5)->create([
            'user_id' => $user->id,
            'good_id' => $product->id,
        ]);

        // 商品詳細ページを開く
        $response = $this->actingAs($user)->get(route('goods.show', $product->id));

        // 「いいね数: 5」が正しく表示されるか確認
        $response->assertSeeText('5');

        // コメントが表示されているか確認
        foreach ($comments as $comment) {
            $response->assertSeeText($comment->content);
            $response->assertSeeText($user->name);
        }
    }

    public function test_product_detail_page_displays_correct_information_categories()
    {
        $user = User::factory()->create();

        // 商品データを作成
        $product = Good::factory()->create([
            'name' => 'Test Product',

            
            'brand' => 'Test Brand',
            'price' => 1000,
            'description' => 'This is a test product.',
            'category' => json_encode(['Electronics', 'Home Appliances']),
            'condition' => 'New',
            'favorites_count' => 5, // 明示的に設定
        ]);

        // コメントデータを追加
        Comment::factory()->count(3)->create([
            'user_id' => $user->id,
            'good_id' => $product->id,
            'content' => 'This is a test comment.',
        ]);

        // コメントを取得
        $comments = Comment::where('good_id', $product->id)->get();

        // いいねデータを追加
        Favorite::factory()->count(5)->create([
            'user_id' => $user->id,
            'good_id' => $product->id,
        ]);

        // 商品詳細ページを開く
        $response = $this->actingAs($user)->get(route('goods.show', $product->id));

        // 「いいね数: 5」が正しく表示されるか確認
        $response->assertSeeText('5');

        // コメントが表示されているか確認
        foreach ($comments as $comment) {
            $response->assertSeeText($comment->content);
            $response->assertSeeText($user->name);
        }
    }
}
