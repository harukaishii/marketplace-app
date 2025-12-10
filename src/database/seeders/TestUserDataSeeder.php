<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\PurchaseHistory;
use App\Models\UserAddress;
use App\Models\TransactionMessage;
use App\Models\Rating;
use App\Enums\ItemStatus;
use App\Enums\ItemCondition;
use App\Enums\PaymentType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class TestUserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // テストユーザーを取得
        $testUser = User::where('email', 'test@example.com')->first();

        if (!$testUser) {
            $this->command->error('テストユーザーが見つかりません');
            return;
        }

        // テストユーザーのUserAddressを取得または作成
        $testUserAddress = UserAddress::where('user_id', $testUser->id)->first();
        if (!$testUserAddress) {
            $testUserAddress = UserAddress::create([
                'user_id' => $testUser->id,
                'postal_code' => '123-4567',
                'address' => '東京都渋谷区1-2-3',
                'building_name' => 'テストビル101',
            ]);
            $this->command->info("✓ テストユーザーの住所を作成しました");
        }

        // 他のユーザーを取得（購入者/出品者として使用）
        $otherUsers = User::where('id', '!=', $testUser->id)->take(5)->get();

        if ($otherUsers->count() < 1) {
            $this->command->error('他のユーザーが見つかりません');
            return;
        }

        // カテゴリーを取得
        $categories = Category::all();

        // 商品画像（サンプル）
        $sampleImages = [
            'Armani+Mens+Clock.jpg',
            'HDD+Hard+Disk.jpg',
            'Leather+Shoes+Product+Photo.jpg',
            'Living+Room+Laptop.jpg',
            'Music+Mic+4632231.jpg',
        ];

        // 画像を storage にコピー（存在する場合のみ）
        foreach ($sampleImages as $imageName) {
            $sampleImagePath = database_path('seeders/sample_images/' . $imageName);
            $destinationPath = 'images/items/' . $imageName;

            if (File::exists($sampleImagePath) && !Storage::disk('public')->exists($destinationPath)) {
                Storage::disk('public')->put(
                    $destinationPath,
                    File::get($sampleImagePath)
                );
            }
        }

        // 1. 出品中のデータ（テストユーザーが出品、まだ誰も購入していない）
        $item1 = Item::create([
            'name' => 'テスト商品（出品中）',
            'brand_name' => 'テストブランド',
            'price' => 5000,
            'detail' => 'テストユーザーが出品した商品です。まだ購入されていません。',
            'image' => 'images/items/' . $sampleImages[0],
            'status' => ItemStatus::AVAILABLE,
            'condition' => ItemCondition::FINE, // 良好
            'listed_by' => $testUser->id,
        ]);

        // カテゴリー紐付け
        if ($categories->count() > 0) {
            $item1->categories()->attach($categories->random(min(2, $categories->count()))->pluck('id'));
        }

        $this->command->info("✓ 出品中のデータを作成しました（商品ID: {$item1->id}）");

        // 2. 出品して取引中のデータ（テストユーザーが出品、他のユーザーが購入）
        $buyer1 = $otherUsers->random();

        // 購入者の住所を取得または作成
        $buyer1Address = UserAddress::where('user_id', $buyer1->id)->first();
        if (!$buyer1Address) {
            $buyer1Address = UserAddress::create([
                'user_id' => $buyer1->id,
                'postal_code' => '456-7890',
                'address' => '東京都港区4-5-6',
                'building_name' => '購入者マンション202',
            ]);
        }

        $item2 = Item::create([
            'name' => 'テスト商品（出品・取引中）',
            'brand_name' => 'テストブランド',
            'price' => 8000,
            'detail' => 'テストユーザーが出品し、現在取引中の商品です。',
            'image' => 'images/items/' . $sampleImages[1],
            'status' => ItemStatus::IN_TRANSACTION,
            'condition' => ItemCondition::GOOD, // 目立った傷や汚れなし
            'listed_by' => $testUser->id,
        ]);

        if ($categories->count() > 0) {
            $item2->categories()->attach($categories->random(min(2, $categories->count()))->pluck('id'));
        }

        // 購入履歴を作成
        $purchase2 = PurchaseHistory::create([
            'user_id' => $buyer1->id,
            'item_id' => $item2->id,
            'user_address_id' => $buyer1Address->id,
            'payment_type' => PaymentType::CARD,
        ]);

        // チャットメッセージを作成（テストユーザーが出品者）
        $this->createMessages($item2, $buyer1, $testUser);

        $this->command->info("✓ 出品して取引中のデータを作成しました（商品ID: {$item2->id}、購入者: {$buyer1->name}）");

        // 3. 購入済みのデータ（他のユーザーが出品、テストユーザーが購入、取引完了）
        $seller1 = $otherUsers->random();

        $item3 = Item::create([
            'name' => 'テスト商品（購入済み）',
            'brand_name' => 'テストブランド',
            'price' => 3000,
            'detail' => 'テストユーザーが購入し、取引が完了した商品です。',
            'image' => 'images/items/' . $sampleImages[2],
            'status' => ItemStatus::SOLD,
            'condition' => ItemCondition::FAIR, // やや傷や汚れあり
            'listed_by' => $seller1->id,
        ]);

        if ($categories->count() > 0) {
            $item3->categories()->attach($categories->random(min(2, $categories->count()))->pluck('id'));
        }

        // 購入履歴を作成
        PurchaseHistory::create([
            'user_id' => $testUser->id,
            'item_id' => $item3->id,
            'user_address_id' => $testUserAddress->id,
            'payment_type' => PaymentType::CARD,
        ]);

        // 評価を作成（テストユーザー → 出品者）
        Rating::create([
            'item_id' => $item3->id,
            'from_user_id' => $testUser->id,
            'to_user_id' => $seller1->id,
            'rating' => 5,
            'comment' => '迅速な対応で、商品も期待通りでした。ありがとうございました！',
        ]);

        $this->command->info("✓ 購入済みのデータを作成しました（商品ID: {$item3->id}、出品者: {$seller1->name}）");
        $this->command->info("  → 評価データを作成しました（テスト太郎 → {$seller1->name}：★5）");

        // 4. 購入して取引中のデータ（他のユーザーが出品、テストユーザーが購入、取引中）
        $seller2 = $otherUsers->random();

        $item4 = Item::create([
            'name' => 'テスト商品（購入・取引中）',
            'brand_name' => 'テストブランド',
            'price' => 12000,
            'detail' => 'テストユーザーが購入し、現在取引中の商品です。',
            'image' => 'images/items/' . $sampleImages[3],
            'status' => ItemStatus::IN_TRANSACTION,
            'condition' => ItemCondition::FINE, // 良好
            'listed_by' => $seller2->id,
        ]);

        if ($categories->count() > 0) {
            $item4->categories()->attach($categories->random(min(2, $categories->count()))->pluck('id'));
        }

        // 購入履歴を作成
        $purchase4 = PurchaseHistory::create([
            'user_id' => $testUser->id,
            'item_id' => $item4->id,
            'user_address_id' => $testUserAddress->id,
            'payment_type' => PaymentType::CONVENIENCE,
        ]);

        // チャットメッセージを作成（テストユーザーが購入者）
        $this->createMessages($item4, $testUser, $seller2);

        $this->command->info("✓ 購入して取引中のデータを作成しました（商品ID: {$item4->id}、出品者: {$seller2->name}）");

        // 5. 出品して売却済みのデータ（テストユーザーが出品、他のユーザーが購入して完了）
        $buyer2 = $otherUsers->random();

        // 購入者の住所を取得または作成
        $buyer2Address = UserAddress::where('user_id', $buyer2->id)->first();
        if (!$buyer2Address) {
            $buyer2Address = UserAddress::create([
                'user_id' => $buyer2->id,
                'postal_code' => '789-0123',
                'address' => '東京都新宿区7-8-9',
                'building_name' => '購入者ハイツ303',
            ]);
        }

        $item5 = Item::create([
            'name' => 'テスト商品（出品・売却済み）',
            'brand_name' => 'テストブランド',
            'price' => 6000,
            'detail' => 'テストユーザーが出品し、売却が完了した商品です。',
            'image' => 'images/items/' . $sampleImages[4],
            'status' => ItemStatus::SOLD,
            'condition' => ItemCondition::GOOD, // 目立った傷や汚れなし
            'listed_by' => $testUser->id,
        ]);

        if ($categories->count() > 0) {
            $item5->categories()->attach($categories->random(min(2, $categories->count()))->pluck('id'));
        }

        // 購入履歴を作成
        PurchaseHistory::create([
            'user_id' => $buyer2->id,
            'item_id' => $item5->id,
            'user_address_id' => $buyer2Address->id,
            'payment_type' => PaymentType::CARD,
        ]);

        // 評価を作成（購入者 → テストユーザー）
        Rating::create([
            'item_id' => $item5->id,
            'from_user_id' => $buyer2->id,
            'to_user_id' => $testUser->id,
            'rating' => 4,
            'comment' => '丁寧な梱包で商品も良かったです。また機会があればよろしくお願いします。',
        ]);

        $this->command->info("✓ 出品・売却済みのデータを作成しました（商品ID: {$item5->id}、購入者: {$buyer2->name}）");
        $this->command->info("  → 評価データを作成しました（{$buyer2->name} → テスト太郎：★4）");

        $this->command->info('');
        $this->command->info('=== テストユーザーデータ作成完了 ===');
        $this->command->info("テストユーザー: {$testUser->name} ({$testUser->email})");
        $this->command->info("- 出品中: 商品ID {$item1->id} (良好)");
        $this->command->info("- 出品・取引中: 商品ID {$item2->id} (目立った傷や汚れなし) + チャット");
        $this->command->info("- 購入済み: 商品ID {$item3->id} (やや傷や汚れあり) + 評価★5");
        $this->command->info("- 購入・取引中: 商品ID {$item4->id} (良好) + チャット");
        $this->command->info("- 出品・売却済み: 商品ID {$item5->id} (目立った傷や汚れなし) + 評価★4受信");
        $this->command->info('');
        $this->command->info('評価データ:');
        $this->command->info("- テスト太郎が送信した評価: 1件（★5）");
        $this->command->info("- テスト太郎が受信した評価: 1件（★4）");
    }

    /**
     * チャットメッセージを作成（現在時刻ベース）
     *
     * @param Item $item
     * @param User $buyer
     * @param User $seller
     * @return void
     */
    private function createMessages(Item $item, User $buyer, User $seller): void
    {
        $messageCount = 5; // 5件のメッセージを作成

        $messages = [
            // 購入者からの最初のメッセージ
            ['user' => $buyer, 'content' => '購入させていただきました。よろしくお願いします。'],
            // 出品者からの返信
            ['user' => $seller, 'content' => 'ご購入ありがとうございます。本日発送予定です。'],
            // 購入者からの返信
            ['user' => $buyer, 'content' => 'ありがとうございます。楽しみにしております。'],
            // 出品者からの発送連絡
            ['user' => $seller, 'content' => '本日発送いたしました。明日到着予定です。'],
            // 購入者からの受取連絡
            ['user' => $buyer, 'content' => '無事に到着しました。ありがとうございました！'],
        ];

        // 現在時刻から遡ってメッセージを作成
        // 最新のメッセージが「今」、古いメッセージは50分前、40分前...となる
        $now = Carbon::now();

        foreach ($messages as $index => $messageData) {
            // 最新のメッセージ（index=4）が現在時刻、古いメッセージほど過去になる
            $minutesAgo = (count($messages) - 1 - $index) * 10;
            $createdAt = $now->copy()->subMinutes($minutesAgo);

            TransactionMessage::create([
                'item_id' => $item->id,
                'user_id' => $messageData['user']->id,
                'content' => $messageData['content'],
                'image' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info("  → {$messageCount}件のメッセージを作成しました（最新: 現在、最古: 40分前）");
    }
}
