<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Good;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_post_comment()
    {
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $this->actingAs($user);

        $good = Good::factory()->create();

        // コメントを投稿
        $response = $this->post(route('comments.store', $good->id), [
            'content' => 'This is a test comment.',
        ]);

        // 商品詳細ページへリダイレクトされる
        $response->assertRedirect(route('goods.show', $good->id));

        // セッションメッセージを確認
        $response->assertSessionHas('success', 'コメントを投稿しました！');

        // コメントが保存されたことを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'good_id' => $good->id,
            'content' => 'This is a test comment.',
        ]);
    }

    public function test_guest_cannot_post_comment()
    {
        // 商品を作成
        $good = Good::factory()->create();

        // コメントを投稿（未認証ユーザー）
        $response = $this->post(route('comments.store', $good->id), [
            'content' => 'This is a test comment.',
        ]);

        // ログインページへリダイレクトされる
        $response->assertRedirect(route('goods.show', $good->id));

        // セッションメッセージを確認
        $response->assertSessionHas('error', 'コメントを投稿するにはログインが必要です。');

        // コメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'content' => 'This is a test comment.',
        ]);
    }

    public function test_comment_cannot_be_256over()
    {
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $this->actingAs($user);

        $good = Good::factory()->create();

        // 256文字以上のコメントを投稿
        $response = $this->post(route('comments.store', $good->id), [
            'content' => '2jDUh9LL7SeQFAalCdgpzbyK3BYnSM65Gur6e9FPTMSy5IOPmc6BXYGm81ekB1H2y9dE6dVdFve29tpG14pmh30pjxFo9WbSIKv8oFqxW38k0m14niRFpGHXj0HuxQBbibYXwGzI6ed36cBsl4QtiIxgDOVAUvTer0ZobJwkkaNE7K54TNgInt9nOSGLdRXf9VaWmUCbUnovE5lKFcOmA4JnVhWmkYm4jPKC8Irpn3LDQy2WjvK6f4kxonkoa3a61', // 256文字以上のコメントを送信
        ]);

        // バリデーションエラーを確認
        $response->assertSessionHasErrors('content');

        // コメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'good_id' => $good->id,
            'content' => '',
        ]);
    }

    public function test_comment_cannnot_be_empty()
    {
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $this->actingAs($user);

        $good = Good::factory()->create();

        // 空のコメントを投稿
        $response = $this->post(route('comments.store', $good->id), [
            'content' => '', // 空のコメントを送信
        ]);

        // バリデーションエラーを確認
        $response->assertSessionHasErrors('content');

        // コメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'good_id' => $good->id,
            'content' => '',
        ]);
    }
}
