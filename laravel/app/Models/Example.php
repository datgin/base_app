<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    protected $fillable = [
        'image',
        'albums',
        'date',
        'status',
        'description',
        'content',
        'views',
        'is_active',
        'price',
        'published',
        'archive'
    ];

    protected $casts = [
        'albums' => 'array',
        'date' => 'date',
        'is_active' => 'boolean'
    ];
}
