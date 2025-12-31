<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'party_id',
        'user_id',
        'tax_id',
        'discountAmount',
        'shipping_charge',
        'discount_percent',
        'discount_type',
        'dueAmount',
        'isPaid',
        'tax_amount',
        'paidAmount',
        'change_amount',
        'totalAmount',
        'actual_total_amount',
        'rounding_amount',
        'rounding_option',
        'lossProfit',
        'paymentType',
        'payment_type_id',
        'invoiceNumber',
        'saleDate',
        'image',
        'sale_data',
        'status',
        'meta',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $id = Sale::where('business_id', auth()->user()?->business_id ?? 1)->count() + 1;
            $model->invoiceNumber = $id;
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetails::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class, 'sale_id');
    }

    public function payment_type(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function dueCollect()
    {
        return $this->hasOne(DueCollect::class);
    }

    public function paymentTypes()
    {
        return $this->belongsToMany(PaymentType::class, 'sale_payment_types')
            ->withPivot('amount', 'ref_code')
            ->withTimestamps();
    }

//    public function getSaleDataAttribute($value)
//    {
//        $data = json_decode($value ?? '{}', true);
//
//        $data['business_id'] = isset($data['business_id']) ? (int) $data['business_id'] : null;
//        $data['user_id'] = isset($data['user_id']) ? (int) $data['user_id'] : null;
//        $data['tax_id'] = isset($data['tax_id']) ? (int) $data['tax_id'] : null;
//        $data['discount_percent'] = isset($data['discount_percent']) ? (float) $data['discount_percent'] : 0;
//        $data['tax_amount'] = isset($data['tax_amount']) ? (float) $data['tax_amount'] : 0;
//        $data['payment_type_id'] = isset($data['payment_type_id']) ? (int) $data['payment_type_id'] : null;
//
//        return $data;
//    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'business_id' => 'integer',
        'invoiceNumber' => 'string',
        'tax_id' => 'integer',
        'user_id' => 'integer',
        'party_id' => 'integer',
        'payment_type_id' => 'integer',
        'discount_percent' => 'double',
        'tax_amount' => 'double',
        'discountAmount' => 'double',
        'dueAmount' => 'double',
        'isPaid' => 'boolean',
        'vat_amount' => 'double',
        'vat_percent' => 'double',
        'paidAmount' => 'double',
        'totalAmount' => 'double',
        'lossProfit' => 'double',
        'change_amount' => 'double',
        'shipping_charge' => 'double',
        'rounding_amount' => 'double',
        'actual_total_amount' => 'double',
        'sale_returns_count' => 'integer',
        'meta' => 'json',
        'sale_data' => 'json'
    ];
}
