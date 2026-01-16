<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDetails extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_id',
        'product_id',
        'purchase_without_tax',
        'purchase_with_tax',
        'profit_percent',
        'sales_price',
        'wholesale_price',
        'dealer_price',
        'quantities',
        'invoice_qty',
        'bonus_qty',
        'pack_size',
        'pack_qty',
        'gross_total_price',
        'vat_amount',
        'discount_amount',
        'net_total',
        'batch_no',
        'expire_date',
        'mfg_date',
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
        'purchase_id' => 'integer',
        'product_id' => 'integer',
        'stock_id' => 'integer',
        'purchase_without_tax' => 'double',
        'purchase_with_tax' => 'double',
        'profit_percent' => 'double',
        'sales_price' => 'double',
        'wholesale_price' => 'double',
        'quantities' => 'integer',
        'invoice_qty' => 'integer',
        'bonus_qty' => 'integer',
        'gross_total_price' => 'double',
        'vat_amount' => 'double',
        'discount_amount' => 'double',
        'net_total' => 'double',
        'business_id' => 'integer',
        'purchase_return_id' => 'integer',
        'purchase_detail_id' => 'integer',
        'return_amount' => 'double',
        'return_qty' => 'integer',
        'dealer_price' => 'double',
    ];
}
