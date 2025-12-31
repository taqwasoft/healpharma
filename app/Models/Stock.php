<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'product_id',
        'batch_no',
        'productStock',
        'purchase_without_tax',
        'profit_percent',
        'sales_price',
        'dealer_price',
        'purchase_with_tax',
        'wholesale_price',
        'mfg_date',
        'expire_date'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'business_id' => 'integer',
        'product_id' => 'integer',
        'productStock' => 'double',
        'purchase_without_tax' => 'double',
        'purchase_with_tax' => 'double',
        'profit_percent' => 'double',
        'sales_price' => 'double',
        'wholesale_price' => 'double',
        'dealer_price' => 'double'
    ];
}
