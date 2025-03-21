<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Good;
use App\Models\Purchase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class MypageTest extends TestCase
{
    use RefreshDatabase;

    //13
    public function test_logged_in_user_can_view_profile_page()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        //ストレージのテスト用ディスクを設定
        Storage::fake('public');

        //ダミーのプロフィール画像を保存
        $filePath = 'profile_images/test_profile.jpg';
        Storage::disk('public')->put($filePath, 'dummy content');

        // テスト用のユーザーを作成
        $user = User::factory()->create([
            'profile_image' => $filePath,
        ]);

        // ユーザーとしてログイン
        $response = $this->actingAs($user)->get('/mypage/profile');

        // ステータス200を確認
        $response->assertStatus(200);

        // ユーザー情報が表示されているか確認
        $response->assertSee($user->name);

        //プロフィール画像が表示されているか確認
        $response->assertSee(asset("storage/{$filePath}"));
    }

    public function test_logged_in_user_can_view_list_of_selling_items()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // テスト用のユーザーを作成
        $user = User::factory()->create();

        // 出品した商品を作成（3つ）
        $goods = Good::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        // ユーザーとしてログイン
        $response = $this->actingAs($user)->get(route('mypage.sell'));

        // ステータス200を確認
        $response->assertStatus(200);

        // 出品した商品が表示されているか確認
        foreach ($goods as $good) {
            $response->assertSee($good->name);
        }
    }

    public function test_logged_in_user_can_view_list_of_purchased_items()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // テスト用のユーザーを作成
        $user = User::factory()->create();

        // 購入した商品を作成（3つ）
        $purchases = Purchase::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        // ユーザーとしてログイン
        $response = $this->actingAs($user)->get(route('mypage.buy'));

        // ステータス200を確認
        $response->assertStatus(200);

        // 購入した商品が表示されているか確認
        foreach ($purchases as $purchase) {
            $response->assertSee($purchase->good->name);
        }
    }

        public function test_logged_in_user_can_view_profile_edit_page_with_correct_initial_values()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // テスト用のユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区1-1-1',
            'building_name' => 'テストマンション',
            'profile_image' => 'profile_images/test.jpg',
        ]);

        // ユーザーとしてログイン
        $response = $this->actingAs($user)->get(route('profile.edit'));

        // ステータス200を確認
        $response->assertStatus(200);

        // 各フィールドが初期値として表示されているか確認
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区1-1-1');
        $response->assertSee('テストマンション');
        $response->assertSee(asset('storage/profile_images/test.jpg'));
    }

    //14
    public function test_logged_in_user_can_update_profile_information()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // ストレージのテスト用ディスクを設定
        Storage::fake('public');

        // テスト用のユーザーを作成
        $user = User::factory()->create();

        // ダミー画像を作成
        $file = UploadedFile::fake()->create('profile.jpg', 100, 'image/jpeg');

        // プロフィール画像をアップロード
        $responseImg = $this->actingAs($user)->post('/profile/imgupdate', [
            'profile_image' => $file,
        ]);

        // ユーザー情報を更新
        $responseUpdate = $this->actingAs($user)->post(route('profile.update'), [
            'name' => '更新ユーザー',
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市2-2-2',
            'building_name' => '更新マンション',
        ]);

        // 更新後に正しくリダイレクトされるか確認
        $responseUpdate->assertRedirect(route('profile.edit'));

        // データベースのユーザー情報が更新されているか確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '更新ユーザー',
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市2-2-2',
            'building_name' => '更新マンション',
        ]);

        // ユーザーの画像情報を取得し直す
        $user->refresh();

        // プロフィール画像が保存されているか確認
        Storage::disk('public')->assertExists($user->profile_image);

        // ファイル名とDBの一致を確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'profile_image' => $user->profile_image,
        ]);
    }

}
