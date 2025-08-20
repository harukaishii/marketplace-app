<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\support\Facades\Hash;
use App\Models\User;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // テストユーザーを1人作成
        User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'profile_completed' => '1',
            'image' => 'images/users/person_1.jpeg',
        ]);

        // Fakerを使ってランダムなユーザーを9人作成
        $faker = Faker::create('ja_JP');

        $userImages = [];
        for ($j = 2; $j <= 10; $j++) {
            $userImages[] = "person_{$j}.jpeg";
        }

        // 9回ループ
        for ($i = 0; $i < 9; $i++) {
            $randomUserImage = $userImages[array_rand($userImages)];
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'email_verified_at' => $faker->boolean(80) ? now() : null,
                'image' => 'images/users/' . $randomUserImage,
            ]);
        }
    }
}
