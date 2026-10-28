<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


//php artisan make:seeder AdminUserSeeder 
//php artisan migrate:fresh --seed
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@real.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('12345678'), // كلمة السر: password
                'role' => 'admin',
                'city_id' => null, // الأدمن لا يحتاج اختيار محافظة
                'email_verified_at' => now(),
            ]
        );
    }
}