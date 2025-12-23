# coachtech フリマアプリ

## 環境構築

### Dockerビルド
1. 必要ディレクトリの作成
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

### Laravel環境構築
1. `docker-compose exec php bash`
2. `composer install`
3. .envファイルを作成
    - `.env.example`をコピーして`.env`ファイルを作成
4. .envファイルに以下の環境変数を追加
- DB設定
    ```ini
        DB_CONNECTION=mysql
        DB_HOST=mysql
        DB_PORT=3306
        DB_DATABASE=laravel_db
        DB_USERNAME=laravel_user
        DB_PASSWORD=laravel_pass
    ```
- メール設定 (MailHog)
    ローカル環境でメールをテストするためにMailHogを使用します。
    ```ini
        MAIL_MAILER=smtp
        MAIL_HOST=mailhog
        MAIL_PORT=1025
        MAIL_USERNAME=null
        MAIL_PASSWORD=null
        MAIL_ENCRYPTION=null
        MAIL_FROM_ADDRESS="no-reply@example.com"
        MAIL_FROM_NAME="${APP_NAME}"
    ```

- 決済処理の設定 (stripe)
    Stripeは決済機能を提供するためのサービスです。開発環境でテスト決済を行うために以下を設定します。
    [Stripeのダッシュボード](https://dashboard.stripe.com/test/apikeys)にアクセスし、テスト用の公開可能キー（**Publishable key**）とシークレットキー（**Secret key**）を取得します。
    `.env`ファイルに以下の環境変数を追加し、取得したキーを貼り付けます。
    ```ini
        STRIPE_KEY=pk_test_****
        STRIPE_SECRET=sk_test_***
    ```

5. アプリケーションキーの作成
    ```bash
    php artisan key:generate
    ```

6. データベースのセットアップ
    ### マイグレーションの実行
    ```bash
    php artisan migrate
    ```

    ### シードの実行
    ```bash
    php artisan db:seed
    ```

    ### ストレージのシンボリックリンク作成（重要！）
    **画像を表示するために必須の手順です。必ず実行してください。**
    ```bash
    php artisan storage:link
    ```

    ### パーミッション設定（必要に応じて）
    ```bash
    chmod -R 775 storage bootstrap/cache
    ```

7. MailHogの起動
    ```bash
    docker-compose up -d mailhog
    ```

8. 画面での操作
    ブラウザで `http://localhost/login` にアクセスしてください

## 使用技術（実行環境）

- **フレームワーク**: Laravel [8.6.12]
- **バックエンド**: PHP [8.3.23]
- **フロントエンド**: HTML, CSS, JavaScript
- **データベース**: MySQL [8.0.26]
- **認証**: Laravel Fortify , MailHog
- **決済処理**: stripe

## ER図
![ER図](ER2.png)

## URL
- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
- MailHog: http://localhost:8025/

## 使い方

### テストアカウント
シーダー実行後、以下の3つのテストアカウントが使用可能です。

#### 出品者アカウント1
- **メールアドレス**: `test1@example.com`
- **パスワード**: `password`
- **ユーザー名**: 山田太郎
- **出品商品**: CO01〜CO05（腕時計、HDD、玉ねぎ、革靴、ノートPC）

#### 出品者アカウント2
- **メールアドレス**: `test2@example.com`
- **パスワード**: `password`
- **ユーザー名**: 佐藤花子
- **出品商品**: CO06〜CO10（マイク、ショルダーバッグ、タンブラー、コーヒーミル、メイクセット）

#### 購入者アカウント
- **メールアドレス**: `test3@example.com`
- **パスワード**: `password`
- **ユーザー名**: 鈴木一郎
- **購入済み商品**: CO01〜CO04

### テストデータの内容

シーダー実行により、以下のテストデータが自動生成されます：

#### 商品データ（10件）
- **CO01（腕時計）** - 売却済み、双方評価完了
- **CO02（HDD）** - 売却済み、双方評価完了
- **CO03（玉ねぎ3束）** - 取引中、購入者のみ評価済み
- **CO04（革靴）** - 取引中、評価なし
- **CO05〜CO10** - 販売中

#### 取引データ
- **購入履歴**: 4件（CO01〜CO04）
- **取引メッセージ**: 11件
- **評価**: 5件
- **コメント**: 6件
- **いいね**: 8件

### 機能テストシナリオ

#### 1. 商品閲覧・検索
1. トップページで商品一覧を確認
2. 検索バーでキーワード検索
3. 商品詳細ページでコメント・いいねを確認

#### 2. 出品機能
1. `test1@example.com`でログイン
2. 「出品」ボタンをクリック
3. 商品情報を入力して出品

#### 3. 購入機能
1. `test3@example.com`でログイン
2. 販売中の商品（CO05〜CO10）を選択
3. 「購入する」ボタンから購入手続き
4. 住所・決済方法を選択（テストカード情報を使用）

#### 4. 取引メッセージ機能
1. `test3@example.com`でログイン
2. マイページ → 購入した商品 → CO03またはCO04
3. 取引画面でメッセージを送信
4. 画像添付・メッセージ編集・削除が可能

#### 5. 評価機能
1. **購入者側テスト**
   - `test3@example.com`でログイン
   - マイページ → 購入した商品 → CO04
   - 「取引を完了する」ボタンをクリック
   - 評価（★1〜5）を選択して送信
   - MailHog（http://localhost:8025/）で出品者宛のメールを確認

2. **出品者側テスト**
   - `test1@example.com`でログイン
   - マイページ → 出品した商品 → CO04
   - ページ読み込み時に評価モーダルが自動表示
   - 評価を送信
   - MailHog（http://localhost:8025/）で購入者宛のメールを確認
   - 双方評価完了により商品ステータスが「売却済み」に変更

#### 6. マイページ機能
1. ログイン後、「マイページ」をクリック
2. 「出品した商品」「購入した商品」タブで確認
3. プロフィール編集から名前・画像・住所を変更

### Stripe決済テスト
開発環境で決済機能をテストするには、Stripeが提供する以下のテストカード情報を使用してください。

| カード番号 | 有効期限 | CVC | 結果 |
| :--- | :--- | :--- | :--- |
| `4242 4242 4242 4242` | 任意の将来の日付 | `123` | 成功 |


### メール送信テスト
1. MailHog（http://localhost:8025/）にアクセス
2. 以下のアクションでメールが送信されることを確認：
   - 会員登録時の確認メール
   - 取引評価時の通知メール

## トラブルシューティング

### 画像が表示されない場合
```bash
# ストレージリンクの再作成
php artisan storage:link

# パーミッション確認
chmod -R 775 storage
```

### データをリセットしたい場合
```bash
# マイグレーションとシーダーを再実行
php artisan migrate:fresh --seed
php artisan storage:link
```

### メールが送信されない場合
```bash
# MailHogの再起動
docker-compose restart mailhog

# .envのMAIL設定を確認
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

## 開発メモ

- 商品画像はS3から自動ダウンロードされ、`storage/app/public/images/items/`に保存されます
- ユーザー画像は`public/images/users/`に配置されています
- 取引メッセージの画像は`storage/app/public/transaction_images/`に保存されます