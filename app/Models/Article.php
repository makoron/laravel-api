<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Article extends Model
{
    protected $fillable = [
        'slug',
        'region_id',
        'prefecture_id',
        'area_id',
        'title',
        'body',
        'body_html',
        'image',
        'image_alt',
        'published_at',
        'is_published',
        'display_order',
        'is_featured',
        'display_order' => 'nullable|integer',
        'is_featured' => 'boolean',
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

