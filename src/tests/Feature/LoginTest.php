<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HelloTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testUserCannotRegisterWithoutName()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 名前なしでフォーム送信
        $response = $this->post('/register', [
            'name' => '', // 名前を空にする
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // バリデーションエラー（名前が必須）
        $response->assertSessionHasErrors(['name']);

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'testuser@example.com'
        ]);
    }

    public function testUserCannotRegisterWithoutEmail()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        //メールアドレス無しでフォームを送信
        $response = $this->post('/register', [
            'name' => 'aaa',
            'email' => '',//メールアドレスを空にする
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        //バリデーションエラー（メールアドレスが必須）
        $response->assertSessionHasErrors(['email']);

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'name' => 'aaa'
        ]);
    }

    public function testUserCannotRegisterWithShortPassword()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // パスワード7文字以下でフォームを送信
        $response = $this->post('/register', [
            'name' => 'aaa',
            'email' => 'testuser@example.com',
            'password' => 'passwor', // 7文字
            'password_confirmation' => 'passwor',
        ]);

        // バリデーションエラー（パスワードが短すぎる）
        $response->assertSessionHasErrors(['password']);

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'testuser@example.com'
        ]);
    }

    public function testUserCannotRegisterWithMismatchedPasswordConfirmation()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // パスワード7文字以下でフォームを送信
        $response = $this->post('/register', [
            'name' => 'aaa',
            'email' => 'testuser@example.com',
            'password' => 'password123', 
            'password_confirmation' => 'password12',//一致していない
        ]);

        // バリデーションエラー（確認用パスワードが一致していない）
        $response->assertSessionHasErrors(['password']);

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'testuser@example.com'
        ]);
    }

    // public function testUserRegistercompleted()
    // {
    //     // 会員登録ページを開く
    //     $response = $this->get('/register');
    //     $response->assertStatus(200);

    //     // パスワード7文字以下でフォームを送信
    //     $response = $this->post('/register', [
    //         'name' => 'aaa',
    //         'email' => 'testuser@example.com',
    //         'password' => 'password123', 
    //         'password_confirmation' => 'password12',//一致していない
    //     ]);

    //     // バリデーションエラー（確認用パスワードが一致していない）
    //     $response->assertSessionHasErrors(['password']);

    //     // データベースにユーザーが作成されていないことを確認
    //     $this->assertDatabaseMissing('users', [
    //         'email' => 'testuser@example.com'
    //     ]);
    // }　登録後の処理のテスト、テスト要件が不明なので保留

    public function testUserCannotLoginWithoutEmail()
    {
        // ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // メールアドレスを入力せずに送信
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123', 
        ]);

        // バリデーションエラー（メールアドレスが必須）
        $response->assertSessionHasErrors(['email']);
    }

    public function testUserCannotLoginWithoutPassword()
    {
        // ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // パスワードを入力せずに送信
        $response = $this->post('/login', [
            'email' => 'testuser@example.com',
            'password' => '', 
        ]);

        // バリデーションエラー（パスワードが必須）
        $response->assertSessionHasErrors(['password']);
    }

    public function testUserCannotLoginWithUnregisteredCredentials()
    {
        // ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 存在しないユーザーの情報でログインを試みる
        $response = $this->post('/login', [
            'email' => 'unregistered@example.com', // 未登録のメールアドレス
            'password' => 'InvalidPassword123', // 適当なパスワード
        ]);

        // 認証エラーが発生することを確認
        $response->assertSessionHasErrors(['email']);

        // ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

    public function testUserCanLoginWithValidCredentials()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'), // ハッシュ化
        ]);

        // ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 正しい情報でログインを試みる
        $response = $this->post('/login', [
            'email' => 'testuser@example.com',
            'password' => 'password123', 
        ]);

        // ログイン成功後のリダイレクトを確認
        $response->assertRedirect('/mypage/profile'); // 適切なリダイレクト先を指定

        // 認証済みであることを確認
        $this->assertAuthenticatedAs($user);
    }

    public function testUserCanLogoutSuccessfully()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(), // メール認証済みにする
        ]);

        // ユーザーをログイン
        $this->actingAs($user);

        // ログアウトリクエストを送信
        $response = $this->post('/logout');

        // ログアウト後のリダイレクトを確認
        $response->assertRedirect('/'); // ここはアプリの設定に合わせる

        // ユーザーがログアウト状態であることを確認
        $this->assertGuest();
    }

}
