<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    protected $fillable = ['name', 'city', 'phone_whatsapp', 'logo', 'is_active'];

    public function properties(): HasMany
    {
        return $table->hasMany(Property::class);
    }
}
