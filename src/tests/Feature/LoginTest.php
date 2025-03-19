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

class LoginTest extends TestCase
{

    use RefreshDatabase;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     // `DB_DATABASE=demo_test` を強制適用
    //     Config::set('database.connections.mysql.database', 'demo_test');
    //     DB::purge('mysql');

    //     // 明示的に `demo_test` へマイグレーション
    //     $this->artisan('migrate');
    // }

    //1
    public function test_user_cannot_register_without_name()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        dump(session()->all()); // セッションの内容を確認
        $response->assertSessionHasErrors(['name']);

        // バリデーションエラー（名前が必須）
        $response->assertSessionHasErrors(['name']);

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'testuser@example.com'
        ]);
    }

    public function test_user_cannot_register_without_email()
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

    public function test_user_cannot_register_with_short_password()
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

    public function test_user_cannot_register_with_mismatched_password_confirmation()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

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

        // ユーザー登録ページを開く
        $response = $this->get(route('register.form'));
        $response->assertStatus(200);

        // ユーザー登録リクエストを送信
        $response = $this->post(route('register'), [
            'name' => 'aaa',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 登録後のデバッグ
        dd(User::all()); // ユーザーが作成されているか確認

        // ユーザーのデータを取得
        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertNotNull($user);

        // メール認証をスキップして、認証済みにする
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
        $response = $this->post('/logout');

        // ログアウト後のリダイレクトを確認
        $response->assertRedirect('/'); // ここはアプリの設定に合わせる

        // ユーザーがログアウト状態であることを確認
        $this->assertGuest();
    }

}
