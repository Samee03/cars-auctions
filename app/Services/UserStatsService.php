<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Computes user statistics with optional scoping to an agent's
 * assigned customers. Keeps query logic out of the widget layer.
 */
class UserStatsService
{
    public function __construct(private readonly ?Admin $admin) {}

    /** @return Builder<User> */
    private function base(): Builder
    {
        if ($this->admin?->hasRole('agent')) {
            return User::where('assigned_agent_id', $this->admin->id);
        }

        return User::query();
    }

    /** @return array{total: int, active: int, verified: int, emailVerified: int, disabled: int} */
    public function totals(): array
    {
        $base = $this->base();

        $total         = (clone $base)->count();
        $active        = (clone $base)->where('status', 'active')->count();
        $verified      = (clone $base)->where('verified_badge', true)->count();
        $emailVerified = (clone $base)->whereNotNull('email_verified_at')->count();
        $disabled      = (clone $base)->where('status', 'disabled')->count();

        return compact('total', 'active', 'verified', 'emailVerified', 'disabled');
    }

    public static function pct(int $part, int $total): string
    {
        return $total > 0 ? round(($part / $total) * 100) . '% of total' : '0% of total';
    }
}