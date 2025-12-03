<?php

namespace Database\Seeders;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        // 外部キー制約を無効にする
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Item::truncate();

        // 外部キー制約を再度有効にする
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ja_JP');

        $categories = Category::all();
        $users = User::all();

        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }
        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // storageディレクトリを作成
        if (!Storage::disk('public')->exists('images/items')) {
            Storage::disk('public')->makeDirectory('images/items');
        }

        $fixedItems = [
            [
                'name' => '腕時計',
                'brand_name' => 'EMPORIO ARMANI',
                'price' => '15000',
                'detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => ItemCondition::FINE,
                'status' => ItemStatus::AVAILABLE,
                'sample_image' => 'Armani+Mens+Clock.jpg',
            ],
            [
                'name' => 'HDD',
                'brand_name' => 'BAFFALO',
                'price' => '5000',
                'detail' => '高速で信頼性の高いハードディスク',
                'condition' => ItemCondition::GOOD,
                'status' => ItemStatus::SOLD,
                'sample_image' => 'HDD+Hard+Disk.jpg',
            ],
            [
                'name' => '玉ねぎ3束',
                'brand_name' => 'MATSUMOTO FARM',
                'price' => '300',
                'detail' => '新鮮な玉ねぎ3束のセット',
                'condition' => ItemCondition::FAIR,
                'status' => ItemStatus::AVAILABLE,
                'sample_image' => 'iLoveIMG+d.jpg',
            ],
            [
                'name' => '革靴',
                'brand_name' => '',
                'price' => '4000',
                'detail' => 'クラシックなデザインの革靴',
                'condition' => ItemCondition::POOR,
                'status' => ItemStatus::AVAILABLE,
                'sample_image' => 'Leather+Shoes+Product+Photo.jpg',
            ],
            [
                'name' => 'ノートPC',
                'brand_name' => 'Apple',
                'price' => '45000',
                'detail' => '高性能なノートパソコン',
                'condition' => ItemCondition::FINE,
                'status' => ItemStatus::AVAILABLE,
                'sample_image' => 'Living+Room+Laptop.jpg',
            ],
            [
                'name' => 'マイク',
                'brand_name' => 'MAXIM',
                'price' => '8000',
                'detail' => '高音質のレコーディング用マイク',
                'condition' => ItemCondition::GOOD,
                'status' => ItemStatus::AVAILABLE,
                'sample_image' => 'Music+Mic+4632231.jpg',
            ],
            [
                'name' => 'ショルダーバッグ',
                'brand_name' => 'PRADA',
                'price' => '3500',
                'detail' => 'おしゃれなショルダーバッグ',
                'condition' => ItemCondition::FAIR,
                'status' => ItemStatus::AVAILABLE,
                'sample_image' => 'Purse+fashion+pocket.jpg',
            ],
            [
                'name' => 'タンブラー',
                'brand_name' => '',
                'price' => '500',
                'detail' => '使いやすいタンブラー',
                'condition' => ItemCondition::POOR,
                'status' => ItemStatus::AVAILABLE,
                'sample_image' => 'Tumbler+souvenir.jpg',
            ],
            [
                'name' => 'コーヒーミル',
                'brand_name' => '',
                'price' => '4000',
                'detail' => '手動のコーヒーミル',
                'condition' => ItemCondition::FINE,
                'status' => ItemStatus::SOLD,
                'sample_image' => 'Waitress+with+Coffee+Grinder.jpg',
            ],
            [
                'name' => 'メイクセット',
                'brand_name' => 'KOSE',
                'price' => '2500',
                'detail' => '便利なメイクアップセット',
                'condition' => ItemCondition::GOOD,
                'status' => ItemStatus::AVAILABLE,
                'sample_image' => '外出メイクアップセット.jpg',
            ]
        ];

        foreach ($fixedItems as $itemData) {
            $randomUser = $users->random();

            // サンプル画像のパス
            $sampleImagePath = database_path('seeders/sample_images/' . $itemData['sample_image']);
            $destinationPath = 'images/items/' . $itemData['sample_image'];

            // サンプル画像が存在する場合はコピー
            if (File::exists($sampleImagePath)) {
                Storage::disk('public')->put(
                    $destinationPath,
                    File::get($sampleImagePath)
                );
                $this->command->info("Copied image: {$itemData['sample_image']}");
            } else {
                $this->command->warn("Sample image not found: {$sampleImagePath}");
            }

            // モデルを使ってデータを作成
            Item::create([
                'name' => $itemData['name'],
                'brand_name' => $itemData['brand_name'],
                'price' => $itemData['price'],
                'detail' => $itemData['detail'],
                'condition' => $itemData['condition'],
                'status' => $itemData['status'],
                'image' => $destinationPath,
                'listed_by' => $randomUser->id,
            ]);
        }

        $this->command->info('Items seeded successfully!');
    }
}
