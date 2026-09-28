<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'scientific_name',
        'image_path',
        'description',
        'symptoms',
        'causes',
        'prevention',
        'recommended_treatment',
        'chemical_treatments',
        'organic_treatments',
        'status',
    ];

    protected $casts = [
        'chemical_treatments' => 'array',
        'organic_treatments' => 'array',
    ];
}
