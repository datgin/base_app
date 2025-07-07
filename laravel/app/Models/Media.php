<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'name',
        'path',
        'format',
        'size',
        'width',
        'height',
        'created_by'
    ];
}
