<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomerResetPassword;
use App\Notifications\CustomerVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'account_type',
        'phone',
        'address_id',
        'email_verified_at',
        'admin_approved_at',
        'assigned_agent_id',
        'company',
        'date_of_birth',
        'status',
        'admin_approval_status',
        'provider',
        'provider_id',
        'backoffice_access',
        'remember_token',
        'terms_accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function searchableAs(): string
    {
        return 'users_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'account_type' => $this->account_type,
            'admin_approval_status' => $this->admin_approval_status,
            'status' => $this->status,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $user) {
            $fullName = trim(implode(' ', array_filter([
                $user->first_name,
                $user->last_name,
            ])));

            if ($fullName !== '') {
                $user->name = $fullName;
            }
        });
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getApprovalStatusAttribute(): ?string
    {
        return $this->admin_approval_status;
    }

    public function getVerifiedBadgeAttribute(): bool
    {
        return ! is_null($this->admin_approved_at);
    }

    public function setNameAttribute(?string $value): void
    {
        $value = trim((string) $value);

        if ($value === '') {
            $this->attributes['first_name'] = null;
            $this->attributes['last_name'] = null;

            return;
        }

        [$firstName, $lastName] = array_pad(explode(' ', $value, 2), 2, null);

        $this->attributes['first_name'] = $firstName;
        $this->attributes['last_name'] = $lastName;
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_agent_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    /** @return MorphToMany<Address> */
    public function addresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable');
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomerResetPassword($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomerVerifyEmail);
    }

    public function defaultShippingAddress()
    {
        return $this->addresses()
            ->where('addresses.type', 'shipping')
            ->where('addresses.is_default', true)
            ->first();
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->full_name)) ?: [];

        return Str::upper(
            collect($parts)
                ->filter()
                ->take(2)
                ->map(fn (string $part) => Str::substr($part, 0, 1))
                ->implode('')
        );
    }

    public function shippingAddresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable')
            ->where('type', 'shipping');
    }

    public function defaultBillingAddress(): ?object
    {
        return $this->billingAddresses()
            ->where('addresses.is_default', true)
            ->where('addresses.type', 'billing')
            ->first();
    }

    public function billingAddresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable')
            ->where('type', 'billing');
    }

    public function isApproved(): bool
    {
        return $this->admin_approval_status === 'approved' || ! is_null($this->admin_approved_at);
    }

    public function isPendingApproval(): bool
    {
        return $this->admin_approval_status === 'pending' && is_null($this->admin_approved_at);
    }

    public function isCompanyBuyer(): bool
    {
        return $this->account_type === 'company';
    }

    public function isIndividualBuyer(): bool
    {
        return $this->account_type === 'individual';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'admin_approved_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
