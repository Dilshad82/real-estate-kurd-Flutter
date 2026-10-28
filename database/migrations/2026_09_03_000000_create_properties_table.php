<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ==========================================
     * 1. إنشاء جدول العقارات الرئيسي (properties)
     * - الشرح: يجمع كافة حقول العقار مع ربطه بجدول المستخدمين (user_id)
     *   وجعل office_id اختيارياً لتفادي مشاكل قيود المفاتيح الأجنبية.
     * ==========================================
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('office_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->string('district')->nullable();
            $table->string('type');
            $table->string('area_name');
            $table->decimal('area_size', 8, 2);
            $table->decimal('price', 12, 2);
            $table->integer('rooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->string('image')->nullable();
            
            // ➕ إحفظ الحقل هنا كحقل نصي اختياري (nullable)
            $table->text('description')->nullable(); 
            
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * ==========================================
     * 2. حذف الجدول عند التراجع (down)
     * ==========================================
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};