<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'catalog_item_id',
        'assigned_provider_id',
        'item_quantity',
        'name',
        'client_phone',
        'event_type',
        'services',
        'order_items',
        'budget',
        'total_client_amount',
        'provider_payout_amount',
        'event_date',
        'location',
        'venue_name',
        'notes',
        'status',
        'dispatch_status',
        'payout_status',
    ];

    protected function casts(): array
    {
        return [
            'services' => 'array',
            'order_items' => 'array',
            'budget' => 'decimal:2',
            'total_client_amount' => 'decimal:2',
            'provider_payout_amount' => 'decimal:2',
            'event_date' => 'date',
            'item_quantity' => 'integer',
        ];
    }

    /**
     * The user who booked the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The specific catalog item requested, if an itemized order.
     */
    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }

    /**
     * The verified provider assigned to dispatch this gig.
     */
    public function assignedProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProviderProfile::class, 'assigned_provider_id');
    }
}
