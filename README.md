# コーチテックフリーマーケット（Pro試験、拡張機能実装版）

## 概要

本アプリは、Laravelを用いて構築されたフリーマーケットアプリです。  
ユーザーは商品を出品・購入できるほか、以下の拡張機能を実装済みです。

---

## 実装済み機能（拡張部分）

### 取引チャット機能  
- ユーザー同士が商品の購入後にチャット形式でやり取り可能  
- チャットの投稿、編集、削除に対応  
- 画像添付対応  

### 取引画面の遷移  
- `/mypage?tab=buy` から購入済み商品にアクセスし、各商品ごとのチャット画面へ遷移可能  

### 取引相手の評価機能  
- 購入完了後、取引相手を5段階で評価可能  
- 購入者／出品者の平均評価をマイページで確認可能  

### 通知・UI改善（未読バッジ等）  
- 新着メッセージがある取引にバッジ表示（未読メッセージ件数）  

### メール通知（MailHog対応）  
- 出品者は購入者が取引完了をした際に、メールで通知を受け取る  

---

## セットアップ手順

### 1. リポジトリクローン & コンテナ起動

```bash
git clone https://github.com/coachtech-material/laravel-docker-template.git coachtechfreemarket
cd coachtechfreemarket

docker-compose build
docker-compose up -d
```

### 2. .env 作成 & マイグレーション

```bash
cp .env.example .env
docker-compose exec app php artisan key:generate
```

`.env` の主な設定値：

```
APP_URL=http://localhost
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

## シーディングについて

本プロジェクトでは、取引チャット機能・評価機能の検証を行いやすくするため、以下の Seeder を実装・改訂しています。

- `CategorySeeder`  
  新たに `categories` テーブルを作成し、カテゴリの初期データを投入できるようにしました。

- `ItemSeeder`  
  既存の `ItemSeeder` を見直し、ユーザー・カテゴリとの関連を明確にしました。

- `TransactionSeeder`  
  試験用として独自に用意し、チャットや評価のテストが可能になる取引データを登録しています。

- `EvaluationSeeder`  
  試験用として独自に用意し、評価閲覧のテストが可能になりました。


### 初期データの内容（例）

- ユーザーは3名登録されています：
  - `User One`, `User Two`: 出品者
  - `User Three`: 購入者  

- `User Three` は複数の商品を購入済みであり、該当するチャットメッセージも含まれています。  

### シーディングの実行方法

```bash
docker-compose exec app php artisan migrate --seed
```

- データ初期化:  
  `docker-compose exec app php artisan migrate:fresh --seed`

---

## 使用技術

| 項目         | 内容               |
|--------------|--------------------|
| フレームワーク | Laravel 8.83.8      |
| 言語         | PHP 7.4.9           |
| データベース | MySQL               |
| 決済         | Stripe              |
| メール      | MailHog（ポート:8025） |
| 実行環境    | Docker              |

---

## 主な画面URL

### 一般ユーザー用

- `/` : トップ（おすすめ商品）
- `/item/{id}` : 商品詳細
- `/purchase/{id}` : 購入画面
- `/mypage` : プロフィールページ
- `/mypage?tab=buy` : 購入履歴
- `mypage.transactions` : 取引中の商品一覧（購入者、販売者共通）  

### 出品者用

- `/sell` : 出品ページ
- `/mypage?tab=sell` : 出品履歴 （マイページ）

---

## チャット機能関連のルート例  

- `/chat/buyer/{purchase_id}` : チャット詳細（購入者用ビュー）  
- `/chat/seller/{purchase_id}` : チャット詳細（出品者用ビュー）  


---

## テスト関連

```bash
cp .env.testing.example .env.testing
docker-compose exec app vendor/bin/phpunit
```

特定のテストだけを実行するには：

```bash
docker-compose exec app vendor/bin/phpunit --filter test_method_name
```
>> 注：この度の試験では単体テストの実装はしておりません  
---

## ストレージ設定

- 商品画像：`public/goods`
- プロフィール画像：`public/profile_images`

シンボリックリンク作成：

```bash
docker-compose exec app php artisan storage:link
```

---

## ER図

![ER図](free-market.png)

---

## 備考

- 本プロジェクトは COACHTECH の Pro 入会テスト用の拡張機能実装課題として取り組んでいます。
- 限られた期間内で設計・開発・検証まで対応しました。
- 拡張機能の実装は要件通りで、特にチャット・評価・通知まわりに注力しています。

```

---
