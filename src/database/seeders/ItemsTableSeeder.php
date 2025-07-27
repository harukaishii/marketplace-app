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

        $fixedItems=[
        [
            'name' => '腕時計',
            'brand_name' => 'EMPORIO ARMANI',
            'price' => '15000',
            'detail' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => ItemCondition::FINE,
            'status' => ItemStatus::AVAILABLE,
            'image' => 'images/items/Armani+Mens+Clock.jpg',
        ],
        [
            'name' => 'HDD',
            'brand_name' => 'BAFFALO',
            'price' => '5000',
            'detail' => '高速で信頼性の高いハードディスク',
            'condition' => ItemCondition::GOOD,
            'status' => ItemStatus::SOLD,
            'image' => 'images/items/HDD+Hard+Disk.jpg',
        ],
        [
            'name' => '玉ねぎ3束',
            'brand_name' => 'MATSUMOTO FARM',
            'price' => '300',
            'detail' => '新鮮な玉ねぎ3束のセット',
            'condition' => ItemCondition::FAIR,
            'status' => ItemStatus::AVAILABLE,
            'image' => 'images/items/iLoveIMG+d.jpg',
        ],
        [
            'name' => '革靴',
            'brand_name' => '',
            'price' => '4000',
            'detail' => 'クラシックなデザインの革靴',
            'condition' => ItemCondition::POOR,
            'status' => ItemStatus::AVAILABLE,
            'image' => 'images/items/Leather+Shoes+Product+Photo.jpg',
        ],
        [
            'name' => 'ノートPC',
            'brand_name' => 'Apple',
            'price' => '45000',
            'detail' => '高性能なノートパソコン',
            'condition' => ItemCondition::FINE,
            'status' => ItemStatus::AVAILABLE,
            'image' => 'images/items/Living+Room+Laptop.jpg',
        ],
        [
            'name' => 'マイク',
            'brand_name' => 'MAXIM',
            'price' => '8000',
            'detail' => '高音質のレコーディング用マイク',
            'condition' => ItemCondition::GOOD,
            'status' => ItemStatus::AVAILABLE,
            'image' => 'images/items/Music+Mic+4632231.jpg',
        ],
        [
            'name' => 'ショルダーバッグ',
            'brand_name' => 'PRADA',
            'price' => '3500',
            'detail' => 'おしゃれなショルダーバッグ',
            'condition' => ItemCondition::FAIR,
            'status' => ItemStatus::AVAILABLE,
            'image' => 'images/items/Purse+fashion+pocket.jpg',
        ],
        [
            'name' => 'タンブラー',
            'brand_name' => '',
            'price' => '500',
            'detail' => '使いやすいタンブラー',
            'condition' => ItemCondition::POOR,
            'status' => ItemStatus::AVAILABLE,
            'image' => 'images/items/Tumbler+souvenir.jpg',
        ],
        [
            'name' => 'コーヒーミル',
            'brand_name' => '',
            'price' => '4000',
            'detail' => '手動のコーヒーミル',
            'condition' => ItemCondition::FINE,
            'status' => ItemStatus::SOLD,
            'image' => 'images/items/Waitress+with+Coffee+Grinder.jpg',
        ],
        [
            'name' => 'メイクセット',
            'brand_name' => 'KOSE',
            'price' => '2500',
            'detail' => '便利なメイクアップセット',
            'condition' => ItemCondition::GOOD,
            'status' => ItemStatus::AVAILABLE,
            'image' => 'images/items/外出メイクアップセット.jpg',
        ]
    ];

        foreach ($fixedItems as $itemData) {
            $randomUser = $users->random(); // ランダムなユーザーを取得
            // モデルを使ってデータを作成 (推奨)
            Item::create(array_merge($itemData, [
                'listed_by' => $randomUser->id, // ランダムなユーザーIDを割り当てる
            ]));
        }
    }
}
