<?php

namespace App\Models;

use App\Extensions\ResetPassword;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Searchable, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'phone',
        'country',
        'city',
        'account_type',
        'status',
        'approval_status',
        'approved_at',
        'terms_accepted_at',
        'provider',
        'provider_id',
        'backoffice_access',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function searchableAs(): string
    {
        return 'users_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (int)$this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'city' => $this->city,
            'account_type' => $this->account_type,
            'approval_status' => $this->approval_status,
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

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->last_name,
        ])));
    }

    public function companyProfile()
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

    /** @return MorphToMany<Address> */
    public function addresses(): MorphToMany
    {
        return $this->morphToMany(Address::class, 'addressable');
    }

    public function defaultShippingAddress()
    {
        return $this->addresses()
            ->wherePivot('type', 'shipping')
            ->wherePivot('is_default', true)
            ->first();
    }

    public function sendPasswordResetNotification($token): void
    {
        try {
            $status = app(ResetPassword::class)->send($this->email, $token);

            if ($status !== Password::RESET_LINK_SENT) {
                Log::error('Failed to send reset password email to user.', [
                    'email' => $this->email,
                    'status' => $status,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send reset password email to user.', [
                'email' => $this->email,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->full_name)) ?: [];

        return Str::upper(
            collect($parts)
                ->filter()
                ->take(2)
                ->map(fn(string $part) => Str::substr($part, 0, 1))
                ->implode('')
        );
    }

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
            'status' => 'boolean',
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
