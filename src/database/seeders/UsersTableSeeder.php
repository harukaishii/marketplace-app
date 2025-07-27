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
    public function run():void
    {
        $faker =  Faker::create('ja_JP');

        $userImages = [];
         for ($j = 1; $j <= 10; $j++) {
             $userImages[] = "person_{$j}.jpg";
         }

        for ($i = 0; $i < 10; $i++) {
            $randomUserImage = $userImages[array_rand($userImages)];
        }

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'email_verified_at' => $faker->boolean(80) ? now() : null,
                'image' => 'images/users/'. $randomUserImage,
            ]);
        }
    }

}
