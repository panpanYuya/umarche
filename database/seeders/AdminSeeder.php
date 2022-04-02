<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //admin権限を持つユーザーを作成する。
        DB::table('admins')->insert([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => Hash::make('password123'),
            'created_at' => '2021/01/02 11:11:11'
        ]);
    }
}
