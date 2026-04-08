<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Support\AuthContext;
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
                        'active' => '',
                        'disabled' => '',
                        default => str($state)->replace('_', ' ')->title()->toString(),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'disabled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'disabled' => Heroicon::NoSymbol,
                        default => Heroicon::CheckCircle,
                    }),
                IconColumn::make('verified_badge')
                    ->label('Verify Badge')
                    ->boolean(),
                TextColumn::make('assignedAgent.name')
                    ->label('Agent')
                    ->placeholder('—')
                    ->toggleable()
                    ->hidden(fn (): bool => AuthContext::isAgent()),
                TextColumn::make('admin_approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->toggleable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('verified_badge')
                    ->options([
                        true => 'Verified',
                        false => 'Pending',
                    ]),
                SelectFilter::make('assigned_agent_id')
                    ->label('Agent')
                    ->relationship('assignedAgent', 'name')
                    ->hidden(fn (): bool => AuthContext::isAgent()),
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
