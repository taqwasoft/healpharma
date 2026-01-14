<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'business_id',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
        'business_id' => 'integer',
    ];
}
