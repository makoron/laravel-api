<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    protected $fillable = ['region_id', 'name', 'slug', 'sort_order'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
