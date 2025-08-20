<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\User;
use Faker\Factory as Faker;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('ja_JP');

        $items = Item::all();
        $users = User::all();

        if ($items->isEmpty() || $users->isEmpty()) {
            echo "Warning: No items or users found. Please seed ItemSeeder and UserSeeder first.\n";
            return;
        }

        // コメントのパターンを定義
        $commentPatterns = [
            'とても良い%sですね！素材も凝ってて気に入りました。',
            '%sの購入を検討中です。状態についてもう少し詳しく知りたいです。',
            'とても素敵な%sですね。特にデザインが流行りで可愛いです！',
            'これは素晴らしい！この状態の良さでこの価格は悩ましいです。購入を検討します！',
            '配送はすぐご対応いただけますか？すぐに使いたいです。お返事待ってます。',
            '写真を見る限りあまり状態が良くないです。傷の箇所をもう少し詳しく明記するべきです。',
        ];

        foreach ($items as $item) {
            for ($i = 0; $i < rand(1, 5); $i++) {
                $pattern = $faker->randomElement($commentPatterns);

                // %sが含まれているかチェックし、商品名に置き換える
                if (strpos($pattern, '%s') !== false) {
                    $comment = sprintf($pattern, $item->name);
                } else {
                    $comment = $pattern;
                }

                DB::table('comments')->insert([
                    'item_id' => $item->id,
                    'user_id' => $users->random()->id,
                    'comment' => $comment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
