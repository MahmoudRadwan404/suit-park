<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class image extends Model
{
    protected $fillable = ['name', 'path', 'type', 'room_id'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(room::class);
    }
}