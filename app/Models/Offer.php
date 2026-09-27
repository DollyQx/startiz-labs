<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'banner_image',
        'discount_type',
        'discount_value',
        'minimum_project_value',
        'maximum_discount',
        'usage_limit',
        'per_client_usage_limit',
        'starts_at',
        'ends_at',
        'is_active',
        'cta_text',
        'cta_url',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'discount_value' => 'decimal:2',
            'minimum_project_value' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'per_client_usage_limit' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'offer_services', 'offer_id', 'service_id')
            ->withTimestamps();
    }
}
