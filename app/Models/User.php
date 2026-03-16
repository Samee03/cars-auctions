<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Extensions\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Database\Factories\UserFactory;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Searchable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company',
        'date_of_birth',
        'status',
        'provider',
        'provider_id',
        'backoffice_access',
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

    protected static function booted(): void
    {
        $added_by_admin = false;
        static::creating(function ($user) use (&$added_by_admin) {
            if (empty($user->password)) {
                $randomPassword = Str::random(10);
                $user->password = bcrypt($randomPassword);
                $added_by_admin = true;
            }
        });

        static::saved(function ($user) use (&$added_by_admin) {
            if ($user->wasRecentlyCreated && $added_by_admin) {
                Password::setDefaultDriver('users');
                $status = Password::sendResetLink(['email' => $user->email]);

                if ($status === Password::RESET_LINK_SENT) {
                    Log::info("Password reset link sent to {$user->email}.");
                } else {
                    Log::error("Failed to send password reset link to {$user->email}. Status: {$status}");
                }
            }
        });
    }

    /**
     * Get the name of the index associated with the model.
     */
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
            'id' => (int)$this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /** @return MorphToMany<Address> */
    public function addresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable');
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function defaultShippingAddress(): object|null
    {
        return $this->shippingAddresses()
            ->wherePivot('addresses.is_default', true)
            ->where('addresses.type', 'shipping')
            ->first();
    }

    public function shippingAddresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable')
            ->where('type', 'shipping');
    }

    public function defaultBillingAddress(): object|null
    {
        return $this->billingAddresses()
            ->wherePivot('addresses.is_default', true)
            ->where('addresses.type', 'billing')
            ->first();
    }

    public function billingAddresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable')
            ->where('type', 'billing');
    }

    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function bidRequests(): HasMany
    {
        return $this->hasMany(BidRequest::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isCompanyBuyer(): bool
    {
        return $this->account_type === 'company';
    }

    public function isIndividualBuyer(): bool
    {
        return $this->account_type === 'individual';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
