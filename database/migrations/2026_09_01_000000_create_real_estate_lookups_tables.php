<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول المحافظات
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"ar": "دهوك", "ku": "Duhok"}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. جدول المناطق الربط مع المحافظة
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->json('name'); // {"ar": "ماسيك", "ku": "Maseek"}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. جدول أنواع العقارات
        Schema::create('property_types', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"ar": "شقة", "ku": "Aparrtment"}
            $table->string('slug')->unique(); // apartment, house, villa
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('property_types');
    }
};