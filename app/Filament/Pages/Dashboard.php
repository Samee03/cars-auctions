<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\UserStatsWidget;
use App\Filament\Widgets\UserStatusChartWidget;
use App\Filament\Widgets\UserVerificationChartWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate')
                    ])
                    ->columns(2)
                    ->columnSpan(3),
            ])->hidden(true);
    }

    public function getWidgets(): array
    {
        return [
            UserStatsWidget::class,
        ];
    }
}