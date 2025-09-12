<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\Admin;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('ja_JP');

        // ログイン確認用 固定管理者
        Admin::create([
            'name' => '管理者 太郎',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
        ]);
    }
}
