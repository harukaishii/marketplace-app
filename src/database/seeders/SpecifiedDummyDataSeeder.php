<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\UserAddress;
use App\Models\PurchaseHistory;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\TransactionMessage;
use App\Models\Rating;
use App\Enums\ItemStatus;
use App\Enums\ItemCondition;
use App\Enums\PaymentType;

class SpecifiedDummyDataSeeder extends Seeder
{
    private $user1;
    private $user2;
    private $user3;
    private $items = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // カテゴリーが存在しない場合は作成
        $this->createCategories();

        // ユーザーを作成
        $this->createUsers();

        // 商品を作成
        $this->createItems();

        // 購入履歴を作成（重要！）
        $this->createPurchaseHistories();

        // コメントを作成
        $this->createComments();

        // いいねを作成
        $this->createFavorites();

        // 取引メッセージを作成
        $this->createTransactionMessages();

        // 評価を作成
        $this->createRatings();

        $this->displaySummary();
    }

    /**
     * カテゴリーを作成
     */
    private function createCategories(): void
    {
        $categories = [
            'ファッション',
            '家電',
            'インテリア',
            'レディース',
            'メンズ',
            'コスメ',
            '本',
            'ゲーム',
            'スポーツ',
            'キッチン',
            'ハンドメイド',
            'アクセサリー',
            'おもちゃ',
            'ベビー・キッズ',
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(['name' => $categoryName]);
        }

        $this->command->info('✅ カテゴリーを作成しました（14件）');
    }

    /**
     * ユーザーを作成
     */
    private function createUsers(): void
    {
        // テストユーザー1（CO01-CO05を出品）
        $this->user1 = User::create([
            'name' => '山田太郎',
            'email' => 'test1@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'image' => 'images/users/person_2.jpeg',
        ]);

        UserAddress::create([
            'user_id' => $this->user1->id,
            'post' => '123-4567',
            'address' => '東京都渋谷区1-2-3',
            'building' => 'テストマンション101',
        ]);

        // テストユーザー2（CO06-CO10を出品）
        $this->user2 = User::create([
            'name' => '佐藤花子',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'image' => 'images/users/person_3.jpeg',
        ]);

        UserAddress::create([
            'user_id' => $this->user2->id,
            'post' => '987-6543',
            'address' => '大阪府大阪市北区4-5-6',
            'building' => 'サンプルビル202',
        ]);

        // テストユーザー3（購入専用ユーザー）
        $this->user3 = User::create([
            'name' => '鈴木一郎',
            'email' => 'test3@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'image' => 'images/users/person_4.jpeg',
        ]);

        UserAddress::create([
            'user_id' => $this->user3->id,
            'post' => '456-7890',
            'address' => '神奈川県横浜市西区7-8-9',
            'building' => 'サンプルハイツ303',
        ]);

        $this->command->info('✅ ユーザーを作成しました（3件）');
    }

    /**
     * 商品を作成
     */
    private function createItems(): void
    {
        $itemsData = [
            // ユーザー1の出品（CO01-CO05）
            [
                'code' => 'CO01',
                'name' => '腕時計',
                'price' => 15000,
                'detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition' => ItemCondition::FINE,
                'status' => ItemStatus::SOLD, // 売却済み
                'user_id' => $this->user1->id,
                'categories' => ['メンズ', 'アクセサリー'],
            ],
            [
                'code' => 'CO02',
                'name' => 'HDD',
                'price' => 5000,
                'detail' => '高速で信頼性の高いハードディスク',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition' => ItemCondition::GOOD,
                'status' => ItemStatus::SOLD, // 売却済み
                'user_id' => $this->user1->id,
                'categories' => ['家電'],
            ],
            [
                'code' => 'CO03',
                'name' => '玉ねぎ3束',
                'price' => 300,
                'detail' => '新鮮な玉ねぎ3束のセット',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition' => ItemCondition::FAIR,
                'status' => ItemStatus::IN_TRANSACTION, // 取引中
                'user_id' => $this->user1->id,
                'categories' => ['キッチン'],
            ],
            [
                'code' => 'CO04',
                'name' => '革靴',
                'price' => 4000,
                'detail' => 'クラシックなデザインの革靴',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition' => ItemCondition::POOR,
                'status' => ItemStatus::IN_TRANSACTION, // 取引中
                'user_id' => $this->user1->id,
                'categories' => ['メンズ', 'ファッション'],
            ],
            [
                'code' => 'CO05',
                'name' => 'ノートPC',
                'price' => 45000,
                'detail' => '高性能なノートパソコン',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition' => ItemCondition::FINE,
                'status' => ItemStatus::AVAILABLE, // 販売中
                'user_id' => $this->user1->id,
                'categories' => ['家電'],
            ],
            // ユーザー2の出品（CO06-CO10）
            [
                'code' => 'CO06',
                'name' => 'マイク',
                'price' => 8000,
                'detail' => '高音質のレコーディング用マイク',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition' => ItemCondition::GOOD,
                'status' => ItemStatus::AVAILABLE, // 販売中
                'user_id' => $this->user2->id,
                'categories' => ['家電'],
            ],
            [
                'code' => 'CO07',
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'detail' => 'おしゃれなショルダーバッグ',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition' => ItemCondition::FAIR,
                'status' => ItemStatus::AVAILABLE, // 販売中
                'user_id' => $this->user2->id,
                'categories' => ['ファッション', 'レディース'],
            ],
            [
                'code' => 'CO08',
                'name' => 'タンブラー',
                'price' => 500,
                'detail' => '使いやすいタンブラー',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition' => ItemCondition::POOR,
                'status' => ItemStatus::AVAILABLE, // 販売中
                'user_id' => $this->user2->id,
                'categories' => ['キッチン'],
            ],
            [
                'code' => 'CO09',
                'name' => 'コーヒーミル',
                'price' => 4000,
                'detail' => '手動のコーヒーミル',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => ItemCondition::FINE,
                'status' => ItemStatus::AVAILABLE, // 販売中
                'user_id' => $this->user2->id,
                'categories' => ['キッチン'],
            ],
            [
                'code' => 'CO10',
                'name' => 'メイクセット',
                'price' => 2500,
                'detail' => '便利なメイクアップセット',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition' => ItemCondition::GOOD,
                'status' => ItemStatus::AVAILABLE, // 販売中
                'user_id' => $this->user2->id,
                'categories' => ['コスメ', 'レディース'],
            ],
        ];

        foreach ($itemsData as $itemData) {
            // S3から画像をダウンロードして保存
            $imagePath = $this->downloadAndSaveImage($itemData['image_url'], $itemData['code']);

            // 商品を作成
            $item = Item::create([
                'name' => $itemData['name'],
                'brand_name' => null,
                'price' => $itemData['price'],
                'detail' => $itemData['detail'],
                'image' => $imagePath,
                'condition' => $itemData['condition']->value,
                'status' => $itemData['status']->value,
                'listed_by' => $itemData['user_id'],
            ]);

            // カテゴリーを紐付け
            foreach ($itemData['categories'] as $categoryName) {
                $category = Category::where('name', $categoryName)->first();
                if ($category) {
                    $item->categories()->attach($category->id);
                }
            }

            $this->items[$itemData['code']] = $item;
        }

        $this->command->info('✅ 商品を作成しました（10件）');
    }

    /**
     * 購入履歴を作成
     */
    private function createPurchaseHistories(): void
    {
        $purchases = [
            // CO01: ユーザー3が購入（カード支払い）- 売却済み
            [
                'user_id' => $this->user3->id,
                'item_id' => $this->items['CO01']->id,
                'user_address_id' => UserAddress::where('user_id', $this->user3->id)->first()->id,
                'payment_type' => PaymentType::CARD,
                'created_at' => now()->subDays(10),
            ],
            // CO02: ユーザー3が購入（コンビニ支払い）- 売却済み
            [
                'user_id' => $this->user3->id,
                'item_id' => $this->items['CO02']->id,
                'user_address_id' => UserAddress::where('user_id', $this->user3->id)->first()->id,
                'payment_type' => PaymentType::CONVENIENCE,
                'created_at' => now()->subDays(8),
            ],
            // CO03: ユーザー3が購入（カード支払い）- 取引中
            [
                'user_id' => $this->user3->id,
                'item_id' => $this->items['CO03']->id,
                'user_address_id' => UserAddress::where('user_id', $this->user3->id)->first()->id,
                'payment_type' => PaymentType::CARD,
                'created_at' => now()->subDays(3),
            ],
            // CO04: ユーザー3が購入（コンビニ支払い）- 取引中
            [
                'user_id' => $this->user3->id,
                'item_id' => $this->items['CO04']->id,
                'user_address_id' => UserAddress::where('user_id', $this->user3->id)->first()->id,
                'payment_type' => PaymentType::CONVENIENCE,
                'created_at' => now()->subDays(2),
            ],
        ];

        foreach ($purchases as $purchase) {
            PurchaseHistory::create($purchase);
        }

        $this->command->info('✅ 購入履歴を作成しました（4件）');
    }

    /**
     * コメントを作成
     */
    private function createComments(): void
    {
        $comments = [
            // CO05（ノートPC）へのコメント - 販売中
            [
                'user_id' => $this->user3->id,
                'item_id' => $this->items['CO05']->id,
                'comment' => 'スペック詳細を教えていただけますか？',
                'created_at' => now()->subDays(2),
            ],
            [
                'user_id' => $this->user1->id,
                'item_id' => $this->items['CO05']->id,
                'comment' => 'CPU: Core i7、メモリ: 16GB、SSD: 512GBです。',
                'created_at' => now()->subDays(1),
            ],
            // CO06（マイク）へのコメント - 販売中
            [
                'user_id' => $this->user1->id,
                'item_id' => $this->items['CO06']->id,
                'comment' => 'USB接続でしょうか？',
                'created_at' => now()->subDays(3),
            ],
            [
                'user_id' => $this->user2->id,
                'item_id' => $this->items['CO06']->id,
                'comment' => 'はい、USB Type-Cで接続できます。',
                'created_at' => now()->subDays(3),
            ],
            // CO07（ショルダーバッグ）へのコメント - 販売中
            [
                'user_id' => $this->user3->id,
                'item_id' => $this->items['CO07']->id,
                'comment' => '素敵なデザインですね！',
                'created_at' => now()->subDays(1),
            ],
            // CO09（コーヒーミル）へのコメント - 販売中
            [
                'user_id' => $this->user1->id,
                'item_id' => $this->items['CO09']->id,
                'comment' => 'どのくらい使用されましたか？',
                'created_at' => now()->subHours(12),
            ],
        ];

        foreach ($comments as $comment) {
            Comment::create($comment);
        }

        $this->command->info('✅ コメントを作成しました（6件）');
    }

    /**
     * いいねを作成
     */
    private function createFavorites(): void
    {
        $favorites = [
            // ユーザー3のいいね
            ['user_id' => $this->user3->id, 'item_id' => $this->items['CO05']->id],
            ['user_id' => $this->user3->id, 'item_id' => $this->items['CO06']->id],
            ['user_id' => $this->user3->id, 'item_id' => $this->items['CO09']->id],
            ['user_id' => $this->user3->id, 'item_id' => $this->items['CO10']->id],
            // ユーザー1のいいね
            ['user_id' => $this->user1->id, 'item_id' => $this->items['CO06']->id],
            ['user_id' => $this->user1->id, 'item_id' => $this->items['CO07']->id],
            ['user_id' => $this->user1->id, 'item_id' => $this->items['CO10']->id],
            // ユーザー2のいいね
            ['user_id' => $this->user2->id, 'item_id' => $this->items['CO05']->id],
        ];

        foreach ($favorites as $favorite) {
            Favorite::create($favorite);
        }

        $this->command->info('✅ いいねを作成しました（8件）');
    }

    /**
     * 取引メッセージを作成
     */
    private function createTransactionMessages(): void
    {
        $messages = [
            // CO01（腕時計）の取引メッセージ - 売却済み
            [
                'item_id' => $this->items['CO01']->id,
                'user_id' => $this->user3->id,
                'content' => '購入させていただきました。よろしくお願いします。',
                'is_read' => true,
                'created_at' => now()->subDays(10),
            ],
            [
                'item_id' => $this->items['CO01']->id,
                'user_id' => $this->user1->id,
                'content' => 'ありがとうございます。本日発送いたします。',
                'is_read' => true,
                'created_at' => now()->subDays(9),
            ],
            [
                'item_id' => $this->items['CO01']->id,
                'user_id' => $this->user3->id,
                'content' => '商品が届きました。ありがとうございました！',
                'is_read' => true,
                'created_at' => now()->subDays(7),
            ],
            // CO02（HDD）の取引メッセージ - 売却済み
            [
                'item_id' => $this->items['CO02']->id,
                'user_id' => $this->user3->id,
                'content' => '購入しました。発送をお願いします。',
                'is_read' => true,
                'created_at' => now()->subDays(8),
            ],
            [
                'item_id' => $this->items['CO02']->id,
                'user_id' => $this->user1->id,
                'content' => '明日発送します。',
                'is_read' => true,
                'created_at' => now()->subDays(7),
            ],
            [
                'item_id' => $this->items['CO02']->id,
                'user_id' => $this->user3->id,
                'content' => '無事に届きました。動作確認OKです。',
                'is_read' => true,
                'created_at' => now()->subDays(5),
            ],
            // CO03（玉ねぎ）の取引メッセージ - 取引中
            [
                'item_id' => $this->items['CO03']->id,
                'user_id' => $this->user3->id,
                'content' => '購入しました。新鮮な状態で届くのを楽しみにしています。',
                'is_read' => true,
                'created_at' => now()->subDays(3),
            ],
            [
                'item_id' => $this->items['CO03']->id,
                'user_id' => $this->user1->id,
                'content' => '本日収穫したものを発送します！',
                'is_read' => true,
                'created_at' => now()->subDays(2),
            ],
            [
                'item_id' => $this->items['CO03']->id,
                'user_id' => $this->user3->id,
                'content' => '商品が届きました。新鮮で美味しそうです！',
                'is_read' => false,
                'created_at' => now()->subDays(1),
            ],
            // CO04（革靴）の取引メッセージ - 取引中
            [
                'item_id' => $this->items['CO04']->id,
                'user_id' => $this->user3->id,
                'content' => '購入しました。よろしくお願いします。',
                'is_read' => true,
                'created_at' => now()->subDays(2),
            ],
            [
                'item_id' => $this->items['CO04']->id,
                'user_id' => $this->user1->id,
                'content' => 'ありがとうございます。発送しました。',
                'is_read' => true,
                'created_at' => now()->subDays(1),
            ],
        ];

        foreach ($messages as $message) {
            TransactionMessage::create($message);
        }

        $this->command->info('✅ 取引メッセージを作成しました（11件）');
    }

    /**
     * 評価を作成
     */
    private function createRatings(): void
    {
        $ratings = [
            // CO01（腕時計）- 双方評価済み（取引完了）
            [
                'item_id' => $this->items['CO01']->id,
                'from_user_id' => $this->user3->id,
                'to_user_id' => $this->user1->id,
                'rating' => 5,
                'comment' => '丁寧な梱包で、商品も綺麗でした。ありがとうございました！',
                'created_at' => now()->subDays(6),
            ],
            [
                'item_id' => $this->items['CO01']->id,
                'from_user_id' => $this->user1->id,
                'to_user_id' => $this->user3->id,
                'rating' => 5,
                'comment' => 'スムーズな取引ありがとうございました。',
                'created_at' => now()->subDays(6),
            ],
            // CO02（HDD）- 双方評価済み（取引完了）
            [
                'item_id' => $this->items['CO02']->id,
                'from_user_id' => $this->user3->id,
                'to_user_id' => $this->user1->id,
                'rating' => 4,
                'comment' => '良い商品でした。ありがとうございました。',
                'created_at' => now()->subDays(4),
            ],
            [
                'item_id' => $this->items['CO02']->id,
                'from_user_id' => $this->user1->id,
                'to_user_id' => $this->user3->id,
                'rating' => 5,
                'comment' => 'ありがとうございました。',
                'created_at' => now()->subDays(4),
            ],
            // CO03（玉ねぎ）- 購入者のみ評価済み（取引中）
            [
                'item_id' => $this->items['CO03']->id,
                'from_user_id' => $this->user3->id,
                'to_user_id' => $this->user1->id,
                'rating' => 5,
                'comment' => '新鮮な商品でした！',
                'created_at' => now()->subHours(12),
            ],
            // CO04（革靴）- 評価なし（取引中）
        ];

        foreach ($ratings as $rating) {
            Rating::create($rating);
        }

        $this->command->info('✅ 評価を作成しました（5件）');
    }

    /**
     * S3から画像をダウンロードしてローカルストレージに保存
     */
    private function downloadAndSaveImage(string $url, string $code): string
    {
        try {
            $imageContent = file_get_contents($url);

            if ($imageContent === false) {
                $this->command->warn("画像のダウンロードに失敗しました: {$url}");
                return 'images/items/default.jpg';
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (empty($extension)) {
                $extension = 'jpg';
            }
            $filename = strtolower($code) . '_' . time() . '.' . $extension;

            Storage::disk('public')->put('images/items/' . $filename, $imageContent);

            $this->command->info("画像を保存しました: {$filename}");

            return 'images/items/' . $filename;
        } catch (\Exception $e) {
            $this->command->error("画像の保存中にエラーが発生しました: {$e->getMessage()}");
            return 'images/items/default.jpg';
        }
    }

    /**
     * 完了メッセージを表示
     */
    private function displaySummary(): void
    {
        $this->command->info('');
        $this->command->info('==============================================');
        $this->command->info('  指定されたダミーデータの作成が完了しました！');
        $this->command->info('==============================================');
        $this->command->info('');
        $this->command->info('【投入されたデータ】');
        $this->command->info('  - カテゴリー: 14件');
        $this->command->info('  - ユーザー: 3件');
        $this->command->info('  - 商品: 10件（CO01〜CO10）');
        $this->command->info('  - 購入履歴: 4件 ⭐');
        $this->command->info('  - コメント: 6件');
        $this->command->info('  - いいね: 8件');
        $this->command->info('  - 取引メッセージ: 11件 ⭐');
        $this->command->info('  - 評価: 5件 ⭐');
        $this->command->info('');
        $this->command->info('【商品のステータス】');
        $this->command->info('  - 売却済み: CO01, CO02（評価完了）');
        $this->command->info('  - 取引中: CO03, CO04（購入済み、メッセージあり）');
        $this->command->info('  - 販売中: CO05-CO10（コメント、いいねあり）');
        $this->command->info('');
        $this->command->info('【テストアカウント】');
        $this->command->info('  1. test1@example.com / password （出品者：CO01-CO05）');
        $this->command->info('  2. test2@example.com / password （出品者：CO06-CO10）');
        $this->command->info('  3. test3@example.com / password （購入者）');
        $this->command->info('');
        $this->command->info('【確認できる機能】');
        $this->command->info('  ✅ 商品一覧・詳細表示');
        $this->command->info('  ✅ コメント機能');
        $this->command->info('  ✅ いいね機能');
        $this->command->info('  ✅ 購入機能');
        $this->command->info('  ✅ 取引メッセージ機能（購入後）');
        $this->command->info('  ✅ 評価機能（双方評価による取引完了）');
        $this->command->info('  ✅ マイページ（出品/購入商品）');
        $this->command->info('');
    }
}
