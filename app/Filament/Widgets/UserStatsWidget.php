<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = User::count();
        $active = User::where('status', 'active')->count();
        $verified = User::where('verified_badge', true)->count();
        $emailVerified = User::whereNotNull('email_verified_at')->count();
        $disabled = User::where('status', 'disabled')->count();

        return [
            Stat::make('Total Users', $total)
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Active Users', $active)
                ->description($total > 0 ? round(($active / $total) * 100) . '% of total' : '0% of total')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Disabled Users', $disabled)
                ->description($total > 0 ? round(($disabled / $total) * 100) . '% of total' : '0% of total')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Badge Verified', $verified)
                ->description('Admin approved users')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning'),

            Stat::make('Email Verified', $emailVerified)
                ->description($total > 0 ? round(($emailVerified / $total) * 100) . '% of total' : '0% of total')
                ->descriptionIcon('heroicon-m-envelope-open')
                ->color('info'),
        ];
    }
}