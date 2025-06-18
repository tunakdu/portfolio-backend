<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'content',
        'category',
        'image',
        'technologies',
        'github_url',
        'live_url',
        'status',
        'featured',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'technologies' => 'array',
        'featured' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
