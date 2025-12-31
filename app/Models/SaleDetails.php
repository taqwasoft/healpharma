<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDetails extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'price',
        'lossProfit',
        'batch_no',
        'quantities',
        'purchase_price',
        'expire_date',
        'stock_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'sale_id' => 'integer',
        'product_id' => 'integer',
        'stock_id' => 'integer',
        'price' => 'double',
        'purchase_price' => 'double',
        'lossProfit' => 'double',
        'quantities' => 'integer',
    ];
}
