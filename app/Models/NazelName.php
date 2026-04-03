<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NazelName extends Model
{
    //
    protected $fillable = [
        'name_ar',
        'name_en',
    ];
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
