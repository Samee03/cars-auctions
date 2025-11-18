<?php

namespace App\Filament\Resources\Admins\Schemas;

use App\Helper\Data;
use App\Models\Admin;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpan(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('email')
                                    ->email()
                                    ->required(),
                                TextInput::make('phone'),
                                Select::make('status')
                                    ->options([true => 'Enabled', false => 'Disabled'])
                                    ->disabled(fn(?Admin $record) => $record === null || $record->roles->first()->name === Data::SUPER_ADMIN),
                                Select::make('roles')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ])
                            ->columns(2)->columnSpan(['lg' => fn(?Admin $record) => $record === null ? 3 : 2]),
                    ]),
            ]);
    }
}
