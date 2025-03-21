<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Good;
use App\Models\Favorite;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;
    //8
    public function test_authenticated_user_can_favorite_a_good()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 商品を作成
        $good = Good::factory()->create();

        // 「いいね」アクションを実行
        $response = $this->post(route('like.store'), [
            'good_id' => $good->id,
        ]);

        // リダイレクトされることを確認
        $response->assertRedirect();

        // 「いいね」がDBに登録されたか確認
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'good_id' => $good->id,
        ]);
    }

    public function test_authenticated_user_can_toggle_favorite_icon()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // ユーザーを作成しログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 商品を作成
        $good = Good::factory()->create();

        // 商品詳細ページを開く（初期状態）
        $response = $this->get(route('goods.show', $good->id));

        // 初期状態で「未いいね」のアイコンが表示されているか確認
        $response->assertSee(asset('images/1fc8ae66e54e525cb4afafb0a04b1deb.png'));

        // いいねを追加
        $this->post(route('like.store'), ['good_id' => $good->id]);

        // **データベースに「いいね」が登録されているか確認**
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'good_id' => $good->id,
        ]);

        // もう一度ページを開く（リダイレクト後のビューを確認）
        $response = $this->get(route('goods.show', $good->id));

        // 「いいね済み」のアイコン（黄色）が表示されているか確認
        $response->assertSee(asset('images/1fc8ae66e54e525cb4afafb0a04b1debyellow.png'));

        // いいね数が1増えていることを確認
        $response->assertSee('1');

        // いいねを解除
        $this->post(route('like.destroy', ['id' => $good->id]));

        // **データベースから「いいね」が削除されているか確認**
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'good_id' => $good->id,
        ]);

        // 再度ページを開く
        $response = $this->get(route('goods.show', $good->id));

        // 「未いいね」のアイコンが再び表示されているか確認
        $response->assertSee(asset('images/1fc8ae66e54e525cb4afafb0a04b1deb.png'));

        // いいね数が0に戻っていることを確認
        $response->assertSee('0');
    }

    public function test_authenticated_user_can_unfavorite_a_good()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 商品を作成
        $good = Good::factory()->create();

        // 事前に「いいね」登録
        Favorite::create([
            'user_id' => $user->id,
            'good_id' => $good->id,
        ]);

        // 「いいね解除」アクションを実行（`like.destroy` に修正）
        $response = $this->post(route('like.destroy', ['id' => $good->id]));

        // リダイレクトされることを確認
        $response->assertRedirect();

        // 「いいね」がDBから削除されたか確認
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'good_id' => $good->id,
        ]);
    }
}
