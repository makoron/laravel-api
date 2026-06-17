<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order'];

    public function prefectures()
    {
        return $this->hasMany(Prefecture::class);
    }
}
