<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'image_id', 'number', 'value'];
    protected $hidden = ['pivot'];
    public function image(): BelongsTo
    {
        return $this->belongsTo(image::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(room::class, 'room_amenities');
    }
}