<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\Item;
use App\Models\PurchaseHistory;
use App\Models\User;
use App\Enums\ItemStatus;

class RatingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 売却済み（SOLD）の商品を取得
        $soldItems = Item::where('status', ItemStatus::SOLD)->get();

        foreach ($soldItems as $item) {
            // 購入履歴を取得
            $purchase = PurchaseHistory::where('item_id', $item->id)->first();

            if (!$purchase) {
                continue;
            }

            // 購入者を取得
            $buyer = User::find($purchase->user_id);

            // 出品者を取得
            $seller = User::find($item->listed_by);

            // 購入者または出品者が存在しない場合はスキップ
            if (!$buyer || !$seller) {
                continue;
            }

            // 購入者と出品者が同じ人物の場合はスキップ（自分で購入した場合）
            if ($buyer->id === $seller->id) {
                continue;
            }

            // 購入者から出品者への評価（80%の確率で作成）
            if (rand(1, 100) <= 80) {
                // 既に評価が存在するかチェック
                $existingRating = Rating::where('item_id', $item->id)
                    ->where('from_user_id', $buyer->id)
                    ->where('to_user_id', $seller->id)
                    ->first();

                if (!$existingRating) {
                    Rating::create([
                        'item_id' => $item->id,
                        'from_user_id' => $buyer->id,     // 購入者
                        'to_user_id' => $seller->id,      // 出品者
                        'rating' => rand(3, 5),           // 3〜5の評価
                        'comment' => $this->getRandomComment('buyer'),
                    ]);
                }
            }

            // 出品者から購入者への評価（70%の確率で作成）
            if (rand(1, 100) <= 70) {
                // 既に評価が存在するかチェック
                $existingRating = Rating::where('item_id', $item->id)
                    ->where('from_user_id', $seller->id)
                    ->where('to_user_id', $buyer->id)
                    ->first();

                if (!$existingRating) {
                    Rating::create([
                        'item_id' => $item->id,
                        'from_user_id' => $seller->id,    // 出品者
                        'to_user_id' => $buyer->id,       // 購入者
                        'rating' => rand(3, 5),           // 3〜5の評価
                        'comment' => $this->getRandomComment('seller'),
                    ]);
                }
            }
        }
    }

    /**
     * ランダムなコメントを取得
     *
     * @param string $type 'buyer' or 'seller'
     * @return string|null
     */
    private function getRandomComment(string $type): ?string
    {
        // 50%の確率でコメントなし
        if (rand(1, 100) <= 50) {
            return null;
        }

        if ($type === 'buyer') {
            $comments = [
                '迅速な発送ありがとうございました！',
                '丁寧な梱包で商品が無事に届きました。',
                '商品の状態も良く、満足しています。',
                'スムーズな取引ができました。ありがとうございました。',
                '説明通りの商品で安心しました。',
                '対応が早くて助かりました！',
                'とても良い取引ができました。',
                '商品の状態が素晴らしかったです。',
            ];
        } else {
            $comments = [
                'スムーズな取引ありがとうございました！',
                '迅速なお支払いありがとうございました。',
                '気持ちの良い取引ができました。',
                'またの機会がありましたらよろしくお願いします。',
                '良い購入者様でした。ありがとうございました。',
                '安心して取引ができました。',
                '丁寧なご連絡ありがとうございました。',
            ];
        }

        return $comments[array_rand($comments)];
    }
}
