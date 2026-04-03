<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'stars',
        'location_ar',
        'location_en',
        'description_ar',
        'description_en',
        'min_price',
        'max_price',
        'type_id',
        'type_name_ar',
        'type_name_en',
        'wehda_name_ar',
        'wehda_name_en',
        'area',
        'look_ar',
        'look_en',
        'nazel_name_id',

    ];

    protected $casts = [
        'stars' => 'integer',
        'price' => 'integer',
        'area' => 'decimal:2',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
    public function thumbnails(): HasMany
    {
        return $this->hasMany(Image::class)->where('type', 'thumbnail');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities')
            ->withPivot('number', 'value')
            ->withTimestamps()
            ->as('details');
    }
    public function nazelName()
    {
        return $this->belongsTo(NazelName::class);
    }

}