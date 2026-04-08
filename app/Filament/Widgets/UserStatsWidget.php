<?php

namespace App\Filament\Widgets;

use App\Filament\Support\AuthContext;
use App\Services\UserStatsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $admin   = AuthContext::admin();
        $isAgent = AuthContext::isAgent();
        $service = new UserStatsService($admin);

        [
            'total'         => $total,
            'active'        => $active,
            'verified'      => $verified,
            'emailVerified' => $emailVerified,
            'disabled'      => $disabled,
        ] = $service->totals();

        return [
            Stat::make($isAgent ? 'My Customers' : 'All Customers', $total)
                ->description($isAgent ? 'Customers assigned to you' : 'All registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Active', $active)
                ->description(UserStatsService::pct($active, $total))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Disabled', $disabled)
                ->description(UserStatsService::pct($disabled, $total))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Badge Verified', $verified)
                ->description('Admin approved customers')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning'),

            Stat::make('Email Verified', $emailVerified)
                ->description(UserStatsService::pct($emailVerified, $total))
                ->descriptionIcon('heroicon-m-envelope-open')
                ->color('info'),
        ];
    }
}