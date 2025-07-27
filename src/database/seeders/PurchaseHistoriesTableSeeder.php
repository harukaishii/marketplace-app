<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // DBファサードをインポート
use App\Enums\PaymentType;
use App\Models\Item;
use App\Models\User;
use App\Models\UserAddress; // UserAddressモデルをインポート
use App\Models\PurchaseHistory;

class PurchaseHistoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $users = User::all();
        $items = Item::all();

        $purchaseHistories = [
            [
                'user_id' => $users->where('id', 1)->first()->id ?? null,
                'item_id' => $items->where('id', 2)->first()->id ?? null,
                'payment_type' => PaymentType::CARD,
            ],
            [
                'user_id' => $users->where('id', 4)->first()->id ?? null,
                'item_id' => $items->where('id', 9)->first()->id ?? null,
                'payment_type' => PaymentType::CONVENIENCE,
            ]
        ];

        foreach ($purchaseHistories as $data) {

            $userAddress = UserAddress::where('user_id', $data['user_id'])->first();

            // user_id と item_id、user_address_id が存在する場合のみ挿入
            if ($data['user_id'] && $data['item_id'] && $userAddress) {
                DB::table('purchase_histories')->insert([
                    'user_id' => $data['user_id'],
                    'item_id' => $data['item_id'],
                    'user_address_id' => $userAddress->id, // 取得したuser_address_idを挿入
                    'payment_type' => $data['payment_type'],
                    'created_at' => now(), // created_atとupdated_atを追加
                    'updated_at' => now(),
                ]);
            } else {
                echo "Warning: Could not create purchase history for user_id " . ($data['user_id'] ?? 'N/A') . " and item_id " . ($data['item_id'] ?? 'N/A') . " (User, Item, or UserAddress not found).\n";
            }
        }
    }
}
