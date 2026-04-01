<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('account_type')
                    ->label('Account')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'company' => 'Company',
                        'individual' => 'Individual',
                        default => str($state)->replace('_', ' ')->title()->toString(),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'company' => 'warning',
                        'individual' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'company' => Heroicon::OutlinedBuildingOffice2,
                        'individual' => Heroicon::OutlinedUser,
                        default => Heroicon::OutlinedQuestionMarkCircle,
                    }),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'disabled' => 'Disabled',
                        default => str($state)->replace('_', ' ')->title()->toString(),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'disabled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'active' => Heroicon::OutlinedCheckCircle,
                        'disabled' => Heroicon::OutlinedNoSymbol,
                        default => Heroicon::OutlinedQuestionMarkCircle,
                    }),
                IconColumn::make('verified_badge')
                    ->label('Verify Badge')
                    ->boolean(),
                TextColumn::make('admin_approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('assignedAgent.name')
                    ->label('Agent')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['assignedAgent']))
            ->filters([
                SelectFilter::make('account_type')
                    ->options([
                        'individual' => 'Individual',
                        'company' => 'Company',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'disabled' => 'Disabled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
