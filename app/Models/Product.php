<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'productName',
        'generic_name',
        'business_id',
        'category_id',
        'unit_id',
        'type_id',
        'manufacturer_id',
        'box_size_id',
        'purchase_without_tax',
        'purchase_with_tax',
        'profit_percent',
        'sales_price',
        'alert_qty',
        'wholesale_price',
        'dealer_price',
        'productCode',
        'images',
        'meta',
        'tax_id',
        'tax_type',
        'product_type',
        'tax_amount',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'productName' => $this->productName,
            'productCode' => $this->productCode,
            'categoryName' => $this->category->categoryName ?? '',
            'unitName' => $this->unit->unitName ?? '',
            // Add stock-related fields if needed
            'sales_price' => $this->stocks->pluck('sales_price')->implode(' '),
            'productStock' => $this->stocks->pluck('productStock')->implode(' '),
            'dealer_price' => $this->stocks->pluck('dealer_price')->implode(' '),
            'purchase_with_tax' => $this->stocks->pluck('purchase_with_tax')->implode(' '),
        ];
    }

    public function searchableAs()
    {
        return 'products_index';
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class)->where('productStock', '>', 0);
    }

    public function scopeNonExpired($query)
    {
        return $query->whereHas('stocks', function ($q) {
            $q->where(function ($q2) {
                $q2->where('expire_date', '>=', today())
                    ->orWhereNull('expire_date');
            });
        });
    }

    public function all_stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function expiring_item()
    {
        return $this->hasOne(Stock::class, 'product_id');
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function manufacterer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function medicine_type(): BelongsTo
    {
        return $this->belongsTo(MedicineType::class, 'type_id');
    }

    public function box_size(): BelongsTo
    {
        return $this->belongsTo(BoxSize::class);
    }

    protected $casts = [
        'meta' => 'json',
        'images' => 'json',
        'business_id' => 'integer',
        'category_id' => 'integer',
        'unit_id' => 'integer',
        'type_id' => 'integer',
        'tax_id' => 'integer',
        'manufacturer_id' => 'integer',
        'box_size_id' => 'integer',
        'purchase_without_tax' => 'double',
        'purchase_with_tax' => 'double',
        'profit_percent' => 'double',
        'sales_price' => 'double',
        'alert_qty' => 'integer',
        'wholesale_price' => 'double',
        'stocks_sum_product_stock' => 'integer',
    ];

    /**
     * Get formatted product name with medicine type and strength
     * Format: (Medicine Type)ProductName-Strength
     * Example: (Tablet)Amlopin-5 mg
     */
    public function getFormattedNameAttribute(): string
    {
        $name = $this->productName;
        
        // Add medicine type if available
        if ($this->medicine_type && $this->medicine_type->name) {
            $name = '(' . $this->medicine_type->name . ')' . $name;
        }
        
        // Add strength if available in meta
        if (isset($this->meta['strength']) && !empty($this->meta['strength'])) {
            $name .= '-' . $this->meta['strength'];
        }
        
        return $name;
    }
}
