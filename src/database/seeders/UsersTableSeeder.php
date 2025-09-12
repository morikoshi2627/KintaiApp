<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // ログイン確認用 固定ユーザー
        User::create([
            'name' => '一般ユーザー 太郎',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);


        // ランダム一般ユーザー
        $faker = Faker::create('ja_JP');
        for ($i = 0; $i < 5; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
            ]);
        }
    }
}
