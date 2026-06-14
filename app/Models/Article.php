<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Article extends Model
{protected $fillable = [
    'title',
    'body',
    'image',
    'image_alt',
    'published_at',
    'is_published',
];

protected $casts = [
    'is_published' => 'boolean',
    'published_at' => 'date',
];

}

