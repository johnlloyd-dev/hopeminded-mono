<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate([
            'username' => 'hopeminded2023',
            'password' => Hash::make('123456789'),
            'user_flag' => 'admin'
        ]);

        Admin::updateOrCreate([
            'user_id' => $user->id,
            'email' => 'admin@hopeminded.com'
        ]);
    }
}
