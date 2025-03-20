<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Testing\Fakes\MailFake;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LoginTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // データベースリセット
        Config::set('database.connections.mysql.database', 'demo_test');
        DB::purge('mysql');
        $this->artisan('migrate');

        // ユーザーが存在する場合は削除
        User::where('email', 'testuser@example.com')->delete();

        session(['errors' => new \Illuminate\Support\MessageBag()]);

        $this->withoutMiddleware();

        // セッションを強制的に開始
        Session::start();
        $this->withSession([]); 
    }

    //1
    public function test_user_cannot_register_without_name()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');

        $response = $this->postJson('/register', [
            'name' => '',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // バリデーションエラー（名前が必須）
        $response->assertJsonValidationErrors(['name']);

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'testuser@example.com'
        ]);
    }

    public function test_user_cannot_register_without_email()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');

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

    public function test_user_cannot_register_with_short_password()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');

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

    public function test_user_cannot_register_with_mismatched_password_confirmation()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');

        // パスワード不一致でフォームを送信
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

    public function test_user_register_completed()
    {
        $this->withoutMiddleware();

        // ユーザー登録リクエストを送信
        $response = $this->post(route('register'), [
            'name' => 'aaa',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // ユーザーのデータを取得
        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertNotNull($user);

        // 認証済みにする
        $user->update(['email_verified_at' => now()]);

        // 認証状態にする
        $this->actingAs($user);

        // プロフィール編集ページへリダイレクトされることを確認
        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200);

        // ユーザーが認証済みであることを確認
        $this->assertAuthenticatedAs($user);
    }

    //2
    public function test_user_cannot_login_without_email()
    {
        // ログインページを開く
        $response = $this->get('/login');

        // メールアドレスを入力せずに送信
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123', 
        ]);

        // バリデーションエラー（メールアドレスが必須）
        $response->assertSessionHasErrors(['email']);
    }

    public function test_user_cannot_login_without_password()
    {
        // ログインページを開く
        $response = $this->get('/login');

        // パスワードを入力せずに送信
        $response = $this->post('/login', [
            'email' => 'testuser@example.com',
            'password' => '', 
        ]);

        // バリデーションエラー（パスワードが必須）
        $response->assertSessionHasErrors(['password']);
    }

    public function test_user_cannot_login_with_unregistered_credentials()
    {
        // ログインページを開く
        $response = $this->get('/login');

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

    public function test_user_can_login_with_valid_credentials()
    {
        // セッションを開始
        $this->withSession([]);

        // ユーザーを作成
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'), // ハッシュ化
        ]);

        // ログインページを開く
        $response = $this->get('/login');

        // 正しい情報でログインを試みる
        $response = $this->post('/login', [
            'email' => 'testuser@example.com',
            'password' => 'password123', 
        ]);

        // 認証済みであることを確認
        $this->assertAuthenticatedAs($user);
    }

    //3
    public function test_user_can_logout_successfully()
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
        $response = $this->withSession([])->post('/logout');

        // ユーザーがログアウト状態であることを確認
        $this->assertGuest();
    }

}
