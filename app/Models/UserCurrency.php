<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCurrency extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'currency_id', 'name', 'country_name', 'code', 'rate', 'symbol', 'position'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'business_id' => 'integer',
        'currency_id' => 'integer',
    ];
}
