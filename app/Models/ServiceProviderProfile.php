<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ServiceProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_name',
        'contact_name',
        'nida_number',
        'business_licence_path',
        'social_media',
        'category',
        'description',
        'status',
        'rejection_reason',
        'verified_at',
        'verified_by',
    ];

    protected $appends = [
        'business_licence_url',
    ];

    public function getBusinessLicenceUrlAttribute(): ?string
    {
        if (! $this->business_licence_path) {
            return null;
        }

        return Storage::disk('public')->url($this->business_licence_path);
    }

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
