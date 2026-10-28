<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

//php artisan db:seed
//php artisan migrate:fresh --seed
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء أو تحديث حساب مدير المنصة (Admin)
        User::updateOrCreate(
            ['email' => 'admin@real.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'city_id' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}