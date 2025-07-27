# coachtech フリマアプリ


## 環境構築

### Dockerビルド
1. 必要ディレクトリの作成
2. DockerDesktopアプリを立ち上げる
3. docker-compose up -d --build

### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. .envファイルを作成
4. .envファイルに以下の環境変数を追加
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

5. アプリケーションキーの作成
php artisan key:generate

6. マイグレーションの実行
php aritsan migrate

7. シーディングの実行
php artisan db:seed

## 使用技術（実行環境）

- PHP8.3.23
- Laravel8.6.12
- MySQL8.0.26
- Fortify

## ER図


## URL

- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/
