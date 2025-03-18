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
        // ストレージのテスト用ディスクを設定
        Storage::fake('public');

        // テスト用のユーザーを作成
        $user = User::factory()->create();

        // ダミーの画像データを作成（画像ファイルとして扱う）
        $file = UploadedFile::fake()->createWithContent('profile.jpg', 'fake_image_content');

        // ユーザーとしてログイン
        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => '更新ユーザー',
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市2-2-2',
            'building_name' => '更新マンション',
            'profile_image' => $file, // 画像を追加
        ]);

        // 更新後に正しくリダイレクトされるか確認
        $response->assertRedirect(route('profile.edit'));

        // データベースが更新されているか確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '更新ユーザー',
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市2-2-2',
            'building_name' => '更新マンション',
        ]);

        // 画像が正しく保存されているか確認
        Storage::disk('public')->assertExists("profile_images/{$file->hashName()}");

        // データベースの画像パスが正しく更新されたか確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'profile_image' => "profile_images/{$file->hashName()}",
        ]);
    }

}
