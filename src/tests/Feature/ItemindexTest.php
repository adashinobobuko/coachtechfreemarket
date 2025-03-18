<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Good;
use App\Models\Favorite;

class ItemindexTest extends TestCase
{
    use RefreshDatabase;

    public function it_displays_recommended_goods()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // ユーザー自身の商品
        $ownGood = Good::factory()->create([
            'user_id' => $user->id,
            'name' => 'My Product',
        ]);

        // 他のユーザーの商品
        $otherGood = Good::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Recommended Item',
        ]);

        // 認証ユーザーとしてログイン
        $this->actingAs($user);

        // おすすめリストを取得
        $response = $this->get(route('index'));

        // レスポンスが正常か確認
        $response->assertStatus(200);

        // ビューに渡されたデータの検証（カテゴリーはチェックしない）
        $response->assertViewHas('goods', function ($goods) use ($ownGood, $otherGood) {
            return $goods->contains($otherGood) && !$goods->contains($ownGood);
        });

    }

    public function test_sold_out_label_is_displayed_on_sold_items()
    {
        // ユーザーと他のユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 売り切れた商品（is_sold = true）
        $soldOutGood = Good::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Sold Out Item',
            'is_sold' => true, // 売り切れ状態
        ]);

        // 認証ユーザーとしてログイン
        $this->actingAs($user);

        // トップページへアクセス
        $response = $this->get(route('index'));

        // 売り切れた商品が表示されているか確認
        $response->assertSee('Sold Out Item'); // 商品名が表示されることを確認
        $response->assertSee('sold'); // "sold" の表示があるかチェック
    }

    public function test_recommended_goods_exclude_users_own_goods()
    {
        // ユーザーを作成
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // ユーザー自身の商品
        $ownGood = Good::factory()->create([
            'user_id' => $user->id,
            'name' => 'My Product',
        ]);

        // 他のユーザーの商品
        $otherGood = Good::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Recommended Item',
        ]);

        // 認証ユーザーとしてログイン
        $this->actingAs($user);

        // おすすめリストを取得
        $response = $this->get(route('index'));

        // レスポンスが正常か確認
        $response->assertStatus(200);

        // ビューに渡されたデータの検証（`goods` がコレクションであり、期待通りのデータを含むか）
        $response->assertViewHas('goods', function ($goods) use ($ownGood, $otherGood) {
            return $goods instanceof \Illuminate\Database\Eloquent\Collection && 
                $goods->contains($otherGood) && 
                !$goods->contains($ownGood);
        });

        // 商品名が表示されているか確認
        $response->assertSeeText($otherGood->name); // 他のユーザーの商品が表示される
        $response->assertDontSeeText($ownGood->name); // 自分の商品が表示されない
    }

    //ここからマイリスト
    public function test_only_favorited_goods_are_displayed_in_mylist()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2つの商品を作成（片方だけ「いいね」する）
        $likedGood = Good::factory()->create([
            'name' => 'Liked Item'
        ]);
        $unlikedGood = Good::factory()->create([
            'name' => 'Unliked Item'
        ]);

        // いいね（Favorite）を追加するために、POSTリクエストを送信
        $this->post(route('like.store'), ['good_id' => $likedGood->id]);

        // マイリストページへアクセス
        $response = $this->get(route('mylist'));

        // 正常に表示されるか確認
        $response->assertStatus(200);

        // いいねした商品だけが表示されているかチェック
        $response->assertSeeText('Liked Item');   // いいねした商品は表示
        $response->assertDontSeeText('Unliked Item'); // いいねしてない商品は非表示
    }

    public function test_sold_out_item_is_displayed_in_mylist()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 他のユーザーを作成（商品出品者）
        $seller = User::factory()->create();

        // すでに売り切れた商品を作成
        $soldOutGood = Good::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Sold Out Item',
            'is_sold' => true, // 最初から売り切れ状態
        ]);

        // ユーザーが「いいね」してマイリストに追加
        Favorite::create([
            'user_id' => $user->id,
            'good_id' => $soldOutGood->id,
        ]);

        // マイリストページへアクセス
        $response = $this->get(route('mylist'));

        // レスポンスが正常か確認
        $response->assertStatus(200);

        // 売り切れた商品がマイリストに表示されているか確認
        $response->assertSeeText('Sold Out Item');

        // 売り切れ表示があるか確認（例: "sold out" を表示する実装なら）
        $response->assertSee('sold'); // "sold" の表示があるかチェック
    }

    public function test_mylist_excludes_users_own_goods_even_if_favorited()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 他のユーザーを作成（商品出品者）
        $seller = User::factory()->create();
    
        // ユーザー自身の商品（自分で「いいね」する）
        $ownGood = Good::factory()->create([
            'user_id' => $user->id,
            'name' => 'My Product',
        ]);

        // 他のユーザーの商品
        $otherGood = Good::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Recommended Item',
        ]);

        // ユーザーが自分の商品に「いいね」する
        Favorite::create([
            'user_id' => $user->id,
            'good_id' => $ownGood->id,
        ]);

        // ユーザーが他のユーザーの商品にも「いいね」する
        Favorite::create([
            'user_id' => $user->id,
            'good_id' => $otherGood->id,
        ]);

        // マイリストページへアクセス
        $response = $this->get(route('mylist'));

        // レスポンスが正常か確認
        $response->assertStatus(200);

        // ビューに渡されたデータの検証（`goods` がコレクションであり、期待通りのデータを含むか）
        $response->assertViewHas('goods', function ($goods) use ($ownGood, $otherGood) {
            return $goods instanceof \Illuminate\Database\Eloquent\Collection && 
                $goods->contains($otherGood) && 
                !$goods->contains($ownGood);
        });

        // 他のユーザーの商品が表示されることを確認
        $response->assertSeeText($otherGood->name); // 他のユーザーの商品が表示される
        $response->assertDontSeeText($ownGood->name); // 自分の出品した商品はマイリストに表示されない
    }

    public function test_uncertified_user_access_to_mylist_shows_nothing()
    {
        // 認証せずにマイリストページへアクセス
        $response = $this->get(route('mylist'));

        // レスポンスが正常（200 OK）であることを確認
        $response->assertStatus(200);

        // ビューに渡される `goods` が空であることを確認
        $response->assertViewHas('goods', function ($goods) {
            return $goods->isEmpty();
        });

        // マイリストに商品が表示されないことを確認
        $response->assertSeeText('マイリストには商品がありません。');
    }

}
