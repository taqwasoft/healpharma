<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'party_id',
        'business_id',
        'user_id',
        'tax_id',
        'discountAmount',
        'shipping_charge',
        'discount_percent',
        'discount_type',
        'tax_amount',
        'dueAmount',
        'paidAmount',
        'change_amount',
        'totalAmount',
        'invoiceNumber',
        'isPaid',
        'status',
        'paymentType',
        'payment_type_id',
        'purchaseDate',
        'purchase_data',
        'note',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $id = Purchase::where('business_id', auth()->user()?->business_id ?? 1)->count() + 1;
            $model->invoiceNumber = $id;
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetails::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class, 'purchase_id');
    }

    public function payment_type(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }
    public function paymentTypes()
    {
        return $this->belongsToMany(PaymentType::class, 'purchase_payment_types')
            ->withPivot('amount', 'ref_code')
            ->withTimestamps();
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isPaid' => 'boolean',
        'party_id' => 'integer',
        'discountAmount' => 'double',
        'dueAmount' => 'double',
        'paidAmount' => 'double',
        'totalAmount' => 'double',
        'change_amount' => 'double',
        'shipping_charge' => 'double',
        'purchase_data' => 'json',
        'purchase_returns_count' => 'integer',
    ];
}
