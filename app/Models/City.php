<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasTranslations;
    protected $fillable = ['name', 'is_active'];
    public array $translatable = ['name'];

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}