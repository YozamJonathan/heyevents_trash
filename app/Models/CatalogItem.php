<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_provider_profile_id',
        'title',
        'category',
        'description',
        'client_price',
        'provider_payout',
        'pricing_unit',
        'location',
        'image_url',
        'stock_quantity',
        'is_available',
        'is_featured',
        'status',
        'admin_notes',
        'approved_at',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'client_price' => 'decimal:2',
            'provider_payout' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * The verified provider profile associated with this item (if direct provider inventory).
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProviderProfile::class, 'service_provider_profile_id');
    }

    /**
     * Bookings requesting this catalog item.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(EventBooking::class, 'catalog_item_id');
    }

    /**
     * Formatted price for client display.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'TZS '.number_format((float) $this->client_price, 0, '.', ',');
    }
}
