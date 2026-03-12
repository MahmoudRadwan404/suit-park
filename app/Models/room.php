<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class room extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'stars',
        'location_ar',
        'location_en',
        'description_ar',
        'description_en',
        'price',
        'type_id',
        'type_name_ar',
        'type_name_en',
        'wehda_name_ar',
        'wehda_name_en',
        'area',
        'look_ar',
        'look_en',
    ];

    protected $casts = [
        'stars' => 'integer',
        'price' => 'integer',
        'area' => 'decimal:2',
    ];
    public function images(): HasMany
    {
        return $this->hasMany(image::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(amenity::class, 'room_amenities');
    }
}