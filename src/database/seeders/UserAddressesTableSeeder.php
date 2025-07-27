<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB; // DBファサードは直接使わないので不要ですが、残していても問題ありません

class UserAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

    
        $users = User::all();

        if ($users->isEmpty()) {
            echo "No users found. Please seed users first.\n";
            return;
        }

        $japaneseBuildingPrefixes = [
            '桜',
            '緑',
            '光',
            '希望',
            '平和',
            '中央',
            '東',
            '西',
            '南',
            '北',
            '新',
            '大',
            '小',
            '第一',
            '第二',
            '第三',
            '富士',
            '山手',
            '海岸',
            'スカイ',
            'シティ',
            'パーク',
            'ヒルズ',
            'レイク',
            'フォレスト',
            'グラン',
            'ロイヤル',
            'エクセル',
            'プレステージ',
            'アーク',
        ];

        $buildingTypes = ['レジデンス', 'ハイツ', 'マンション', 'コーポ', 'アパート', 'ヴィラ'];

        $users->each(function ($user) use ($faker, $japaneseBuildingPrefixes, $buildingTypes) {
            UserAddress::create([
                'user_id' => $user->id,
                'post' => $faker->postcode(),
                'address' => $faker->address(),
                'building' => $faker->randomElement($japaneseBuildingPrefixes) . $faker->randomElement($buildingTypes) . $faker->randomNumber(3, true) . '号室',
            ]);
        });
    }
}
