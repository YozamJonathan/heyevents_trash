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
        'name',
        'event_type',
        'services',
        'budget',
        'event_date',
        'location',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'services' => 'array',
            'budget' => 'decimal:2',
            'event_date' => 'date',
        ];
    }

    /**
     * The user who booked the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
