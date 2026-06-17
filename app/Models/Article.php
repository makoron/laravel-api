<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Article extends Model
{
    protected $fillable = [
        'region_id',
        'prefecture_id',
        'area_id',
        'title',
        'body',
        'image',
        'image_alt',
        'published_at',
        'is_published',
        'region_id',
        'prefecture_id',
        'area_id',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

}

