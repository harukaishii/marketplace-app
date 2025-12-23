<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            // カテゴリーなどの基本マスターデータは残す
            CategoriesTableSeeder::class,

            // 指定されたダミーデータ（CO01-CO10）を投入
            // これがユーザー、商品、カテゴリー紐付けを作成します
            SpecifiedDummyDataSeeder::class,

            // 以下は不要になったためコメントアウト
            // UsersTableSeeder::class,              // ← SpecifiedDummyDataSeederで作成
            // ItemsTableSeeder::class,              // ← SpecifiedDummyDataSeederで作成
            // ItemCategoriesTableSeeder::class,     // ← SpecifiedDummyDataSeederで作成
            // CommentsTableSeeder::class,           // ← 不要（後で手動で追加可能）
            // FavoritesTableSeeder::class,          // ← 不要（後で手動で追加可能）
            // UserAddressesTableSeeder::class,      // ← SpecifiedDummyDataSeederで作成
            // PurchaseHistoriesTableSeeder::class,  // ← 不要（取引データは手動テスト）
            // TransactionMessageTableSeeder::class, // ← 不要（取引データは手動テスト）
            // RatingTableSeeder::class,             // ← 不要（評価データは手動テスト）
            // TestUserDataSeeder::class,            // ← 完全に置き換え
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 実行完了メッセージ
        $this->command->info('');
        $this->command->info('==============================================');
        $this->command->info('  データベースのシーディングが完了しました！');
        $this->command->info('==============================================');
        $this->command->info('');
        $this->command->info('投入されたデータ:');
        $this->command->info('  - カテゴリー: 14件');
        $this->command->info('  - ユーザー: 3件');
        $this->command->info('  - 商品: 10件（CO01〜CO10）');
        $this->command->info('');
        $this->command->info('テストアカウント:');
        $this->command->info('  1. test1@example.com / password （出品者：CO01-CO05）');
        $this->command->info('  2. test2@example.com / password （出品者：CO06-CO10）');
        $this->command->info('  3. test3@example.com / password （購入者）');
        $this->command->info('');
        $this->command->info('次のステップ:');
        $this->command->info('  1. php artisan storage:link を実行（未実行の場合）');
        $this->command->info('  2. http://localhost にアクセス');
        $this->command->info('  3. テストアカウントでログイン');
        $this->command->info('');
    }
}
