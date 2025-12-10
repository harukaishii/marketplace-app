<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionMessage;
use App\Models\Item;
use App\Models\PurchaseHistory;
use App\Models\User;
use App\Enums\ItemStatus;
use Carbon\Carbon;

class TransactionMessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 取引中または売却済みの商品を取得
        $items = Item::whereIn('status', [ItemStatus::IN_TRANSACTION, ItemStatus::SOLD])->get();

        foreach ($items as $item) {
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

            // 各商品に3〜8件のメッセージを作成
            $messageCount = rand(3, 8);

            for ($i = 0; $i < $messageCount; $i++) {
                // 購入者と出品者を交互にメッセージ送信者にする
                $isFromBuyer = ($i % 2 === 0);
                $userId = $isFromBuyer ? $buyer->id : $seller->id;

                // メッセージ内容
                $content = $this->getRandomMessage($i, $isFromBuyer, $messageCount);

                // 作成日時（購入日時から順番に）
                $createdAt = Carbon::parse($purchase->created_at)->addMinutes(10 * ($i + 1));

                TransactionMessage::create([
                    'item_id' => $item->id,
                    'user_id' => $userId,
                    'content' => $content,
                    'image' => null, // 画像はなし
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }

    /**
     * ランダムなメッセージを取得
     *
     * @param int $index メッセージの順番
     * @param bool $isFromBuyer 購入者からのメッセージか
     * @param int $totalMessages 総メッセージ数
     * @return string
     */
    private function getRandomMessage(int $index, bool $isFromBuyer, int $totalMessages): string
    {
        if ($index === 0) {
            // 最初のメッセージ
            if ($isFromBuyer) {
                $messages = [
                    '購入させていただきました。よろしくお願いします。',
                    'この度は購入させていただきます。楽しみにしています。',
                    '購入しました。発送を楽しみにお待ちしております。',
                ];
            } else {
                $messages = [
                    'ご購入ありがとうございます。本日発送予定です。',
                    'この度はご購入いただきありがとうございます。',
                    'ご購入ありがとうございます。迅速に発送いたします。',
                ];
            }
        } elseif ($index === $totalMessages - 1) {
            // 最後のメッセージ
            if ($isFromBuyer) {
                $messages = [
                    '無事に到着しました。ありがとうございました！',
                    '商品受け取りました。丁寧な梱包ありがとうございます。',
                    '本日届きました。良い取引ができて嬉しいです。',
                ];
            } else {
                $messages = [
                    '無事に届いたようで安心しました。ありがとうございました。',
                    '受け取り確認ありがとうございます。またの機会がありましたら。',
                    'この度はありがとうございました。',
                ];
            }
        } else {
            // 中間のメッセージ
            if ($isFromBuyer) {
                $messages = [
                    '承知しました。',
                    'ありがとうございます。',
                    '楽しみにしております。',
                    '了解です。',
                    '迅速なご対応感謝します。',
                ];
            } else {
                $messages = [
                    '本日発送いたしました。',
                    '明日到着予定です。',
                    '何かございましたらお気軽にご連絡ください。',
                    'よろしくお願いいたします。',
                    '丁寧に梱包いたしました。',
                ];
            }
        }

        return $messages[array_rand($messages)];
    }
}
