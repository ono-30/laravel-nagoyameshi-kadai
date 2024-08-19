<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*自分用の管理者アカウント */
        /*$admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        */

        /*レビュー用の管理者アカウント */
        $admin = new Admin();
        $admin->email = 'admin2@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
    }
}
