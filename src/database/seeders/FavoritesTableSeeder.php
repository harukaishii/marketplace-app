<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\User;

class FavoritesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = Item::all();
        $users = User::all();

        if ($items->isEmpty() || $users->isEmpty()) {
            echo "Warning: No items or users found. Please seed ItemSeeder and UserSeeder first.\n";
            return;
        }

        foreach ($users as $user) {

            $numberOfFavorites = rand(0, 10);
            $favoriteItems = $items->random($numberOfFavorites);

            foreach ($favoriteItems as $item) {
                if (!DB::table('favorites')
                    ->where('user_id', $user->id)
                    ->where('item_id', $item->id)
                    ->exists()) {
                    DB::table('favorites')->insert([
                        'user_id' => $user->id,
                        'item_id' => $item->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
