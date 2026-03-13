<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'source',
        'stock_number',
        'vin',
        'make',
        'model',
        'trim',
        'year',
        'mileage',
        'mileage_unit',
        'price_jpy',
        'price_usd',
        'auction_grade',
        'condition_report',
        'seller_notes',
        'location',
        'status',
        'vehicle_url',
        'is_featured',
        'price_tier',
    ];

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    public function searchableAs(): string
    {
        return 'vehicles_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'trim' => $this->trim,
            'year' => $this->year,
            'price_jpy' => $this->price_jpy,
            'mileage' => $this->mileage,
            'status' => $this->status,
            'source' => $this->source,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class)->orderBy('sort_order');
    }

    public function mainImage(): HasMany
    {
        return $this->hasMany(VehicleImage::class)->where('is_main', true);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function bidRequests(): HasMany
    {
        return $this->hasMany(BidRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTitleAttribute(): string
    {
        return trim("{$this->make} {$this->model}" . ($this->trim ? " {$this->trim}" : ''));
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByPriceTier($query, string $tier)
    {
        return $query->where('price_tier', $tier);
    }

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'mileage' => 'integer',
            'price_jpy' => 'integer',
            'price_usd' => 'integer',
            'is_featured' => 'boolean',
        ];
    }
}
