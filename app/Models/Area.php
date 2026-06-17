<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['prefecture_id', 'name', 'slug', 'sort_order'];

    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class);
    }
}
