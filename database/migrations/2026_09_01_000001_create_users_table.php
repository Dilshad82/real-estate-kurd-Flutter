<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // ➕ عمود الدور (role) المطلوبة في الـ Seeder
            $table->string('role')->default('office'); // admin أو office

            // المحافظة
            $table->unsignedBigInteger('city_id')->nullable();

            // ➕ حقول الرخصة والموافقة
            $table->string('office_license')->nullable(); // صورة إجازة المكتب
            $table->boolean('is_approved')->default(false); // حالة موافقة المدير

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
