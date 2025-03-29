# コーチテックフリーマーケット (coachtechfreemarket)  

## プロジェクト概要  
コーチテックフリーマーケットは、ユーザーが商品を出品・購入できるオンラインマーケットプレイスです。  
登録したユーザーは商品をお気に入り登録（マイリスト）したり、Stripeを利用して商品購入も可能です。  

---

## 環境構築  

### 1. クローン & Docker起動  
```bash  
# リポジトリをクローン  
git clone https://github.com/coachtech-material/laravel-docker-template.git coachtechfreemarket
cd coachtechfreemarket  

# Dockerのビルドと起動  
docker-compose build  
docker-compose up -d  
### 2. .envファイル作成とmigrate方法  
# .env の作成と編集
cp .env.example .env
# APP_KEYの生成
docker-compose exec app php artisan key:generate  
APP_URL=http://localhost  
DB_CONNECTION=mysql  
DB_HOST=mysql  
DB_PORT=3306  
DB_DATABASE=laravel_db  
DB_USERNAME=laravel_user  
DB_PASSWORD=laravel_pass  

# マイグレーションの実行  
docker-compose exec app php artisan migrate --seed  
# データをリセットする場合  
docker-compose exec app php artisan migrate:fresh --seed  

.envは.env.exampleを参照にしてください。  

###　使用技術 & 実行環境  
フレームワーク & 言語  
Laravel 8.83.8  

PHP 7.4.9   

データベース  
MySQL 15.  

Docker環境  
Laravel App (app コンテナ)  

MySQL (mysql コンテナ)  
  
MailHog (mailhog コンテナ)  
# .env での MailHog 設定  
MAIL_MAILER=smtp  
MAIL_HOST=mailhog  
MAIL_PORT=1025  
MAIL_USERNAME=null  
MAIL_PASSWORD=null  
MAIL_ENCRYPTION=null  
MAIL_FROM_ADDRESS="noreply@example.com"  
MAIL_FROM_NAME="Coachtech Freemarket"  
  
決済サービス  
Stripe  

# MailHog へのアクセス  
http://localhost:8025/  

# PHPUnit 実行およびテスト用の環境ファイルの作成 
まずはデータベースを用意します。  
$ mysql -u root -p  
> CREATE DATABASE demo_test;  
> SHOW DATABASES;  
config/database.phpにテストサーバーのコードは記載済みです。  
docker-compose exec app vendor/bin/phpunit  
テスト用に.env.testingがございますのでテスト実行時はそちらをお使いください。 
cp .env.testing.example .env.testing  
特定のメソッドのみ実行  
特定のテストメソッドだけ実行したい場合は、--filter オプションを使用します。  
docker-compose exec app vendor/bin/phpunit --filter  test_it_displays_sell_form_and_stores_good_successfully  
--filter の後には、テストクラス内のメソッド名を指定します。  

## ER図  
![ER図](/home/bobuko/coachtech/laravel/coachtechfreemarket/free-market.drawio.png)
## URL  
### 開発環境  
- **開発環境URL:** [http://localhost/](http://localhost/)  

### ユーザー向けページ  
- `/` 　　　　　トップページ（おすすめ商品） ※検索時は `search` パラメータ付き  
- `/?tab=mylist`　マイリストページ（お気に入り一覧） ※検索時は `search` パラメータ付き  
- `/register`　　会員登録画面  
- `/login`　　　会員ログイン画面  
- `/item/{id}`　 商品詳細画面  

### 購入・配送関連  
- `/purchase/{id}`　　　　　商品購入画面  
- `/purchase/address/{id}`　お届け先住所変更ページ  

### 出品関連  
- `/sell`　商品出品画面  

### マイページ関連  
- `/mypage`　　　　　　プロフィール画面  
- `/mypage?tab=buy`　　マイページ：購入した商品一覧  
- `/mypage?tab=sell`　 マイページ：出品した商品一覧  
  
## 機能一覧  
#ユーザー向け機能  
商品の検索、購入、マイリスト登録  

会員登録・ログイン  

商品出品・購入履歴の確認  

配送先住所の登録・変更  

#出品者向け機能  
商品の出品、編集、削除  

出品履歴の管理  

---  

## 画像アップロードと表示  
# 画像保存のパス  
- 画像は `public/goods` に保存されます。  
- URL 形式：`http://localhost/storage/app/public`  
# 画像アップロードのパス  
- 商品画像: `public/goods`  
- プロフィール画像: `public/profile_images`  
- シンボリックリンク: `public/storage`  

# シンボリックリンクの作成  
docker-compose exec app php artisan storage:link  
