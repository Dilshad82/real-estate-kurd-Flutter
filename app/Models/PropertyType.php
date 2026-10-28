<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PropertyType extends Model
{
    use HasTranslations;
    protected $fillable = ['name', 'slug', 'is_active'];
    public array $translatable = ['name'];
}
