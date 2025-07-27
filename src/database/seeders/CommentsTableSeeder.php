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

        // アイテムとユーザーが存在する場合のみコメントを生成
        if ($items->isEmpty() || $users->isEmpty()) {
            echo "Warning: No items or users found. Please seed ItemSeeder and UserSeeder first.\n";
            return;
        }

        foreach ($items as $item) {
            for ($i = 0; $i < rand(1, 5); $i++) {
                DB::table('comments')->insert([
                    'item_id' => $item->id,
                    'user_id' => $users->random()->id,
                    'comment' => $faker->realText(rand(50, 200)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
