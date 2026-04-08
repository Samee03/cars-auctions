<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Support\AuthContext;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string|Htmlable
    {
        return AuthContext::isAgent() ? 'My Customers' : 'Customers';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (AuthContext::isAgent()) {
            return 'Showing customers assigned to you — ' . AuthContext::admin()->name;
        }

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Create Customer'),
        ];
    }
}