<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\District;
use App\Models\PropertyType;

//php artisan db:seed --class=RealEstateLookupSeeder
class RealEstateLookupSeeder extends Seeder
{   
    public function run(): void
    {
        // إنشاء المحافظات والمناطق
        $duhok = City::create(['name' => ['ar' => 'دهوك', 'ku' => 'Duhok']]);
        $erbil = City::create(['name' => ['ar' => 'أربيل', 'ku' => 'Hewlêr']]);
        $sulaymaniyah = City::create(['name' => ['ar' => 'السليمانية', 'ku' => 'Silêmanî']]);

        // مناطق دهوك
        $districtsDuhok = ['ماسيك', 'شندوكا', 'كرو فري', 'مالطا', 'زاخو', 'سميل', 'عصابة'];
        foreach ($districtsDuhok as $dist) {
            District::create(['city_id' => $duhok->id, 'name' => ['ar' => $dist, 'ku' => $dist]]);
        }

        // مناطق أربيل
        $districtsErbil = ['عنكاوا', 'نزهتي', 'شورش', '100 متري', 'بخنياري'];
        foreach ($districtsErbil as $dist) {
            District::create(['city_id' => $erbil->id, 'name' => ['ar' => $dist, 'ku' => $dist]]);
        }

        // أنواع العقارات
        PropertyType::create(['name' => ['ar' => 'شقة', 'ku' => 'Aparrtment'], 'slug' => 'apartment']);
        PropertyType::create(['name' => ['ar' => 'منزل / بيت', 'ku' => 'House'], 'slug' => 'house']);
        PropertyType::create(['name' => ['ar' => 'فيلا', 'ku' => 'Villa'], 'slug' => 'villa']);
        PropertyType::create(['name' => ['ar' => 'أرض', 'ku' => 'Zewî'], 'slug' => 'land']);
        PropertyType::create(['name' => ['ar' => 'مكتب تجاري', 'ku' => 'Office'], 'slug' => 'office']);
    }
}