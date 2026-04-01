<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        TextEntry::make('first_name'),
                        TextEntry::make('last_name'),
                        TextEntry::make('email')
                            ->label('Email address'),
                        TextEntry::make('phone')
                            ->placeholder('—'),
                        TextEntry::make('date_of_birth')
                            ->date()
                            ->placeholder('—'),
                    ])
                    ->columns(2),
                Section::make('Account')
                    ->schema([
                        TextEntry::make('account_type')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'disabled' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),
                Section::make('Verification & approval')
                    ->schema([
                        TextEntry::make('email_verified_at')
                            ->dateTime()
                            ->placeholder('—'),
                        IconEntry::make('verified_badge')
                            ->label('Verified badge')
                            ->boolean(),
                        TextEntry::make('admin_approved_at')
                            ->label('Admin approved at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('terms_accepted_at')
                            ->label('Terms accepted at')
                            ->dateTime()
                            ->placeholder('—'),
                    ])
                    ->columns(2),
                Section::make('Assignment & address')
                    ->schema([
                        TextEntry::make('assignedAgent.name')
                            ->label('Assigned agent')
                            ->placeholder('—'),
                        TextEntry::make('primary_address')
                            ->label('Primary address')
                            ->getStateUsing(function (User $record): string {
                                $a = $record->address;

                                return $a ? trim(implode(', ', array_filter([
                                    $a->street,
                                    $a->city,
                                    $a->state,
                                    $a->country,
                                    $a->zip,
                                ]))) : '—';
                            }),
                    ])
                    ->columns(2),
                Section::make('Timestamps')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
