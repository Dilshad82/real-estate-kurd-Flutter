<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class District extends Model
{
    use HasTranslations;
    protected $fillable = ['city_id', 'name', 'is_active'];
    public array $translatable = ['name'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
