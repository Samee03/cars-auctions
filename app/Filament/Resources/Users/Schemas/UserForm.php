<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Address;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required()
                                    ->unique()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(30),
                                DatePicker::make('date_of_birth')
                                    ->native(false),
                            ])
                            ->columns(2),
                    ]),
                Section::make('Account')
                    ->schema([
                        TextInput::make('account_type')
                            ->readOnly()
                            ->disabled(),
                        FormSelect::make('status')
                            ->options([
                                'active' => 'Active',
                                'disabled' => 'Disabled',
                            ])
                            ->required()
                            ->native(false),
                        Toggle::make('verified_badge')
                            ->label('Verified badge')
                            ->live()
                            ->afterStateUpdated(function (mixed $state, Set $set): void {
                                if ($state) {
                                    $set('admin_approved_at', now());
                                } else {
                                    $set('admin_approved_at', null);
                                }
                            }),
                    ])
                    ->columns(2),
                Section::make('Company details')
                    ->relationship('companyProfile')
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('company_name')
                                    ->label('Company name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('registration_number')
                                    ->label('Registration number')
                                    ->maxLength(255),
                                TextInput::make('company_phone')
                                    ->label('Company phone')
                                    ->tel()
                                    ->maxLength(30),
                            ])
                            ->columns(2),
                        Textarea::make('company_address')
                            ->label('Company address')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (callable $get): bool => $get('account_type') === 'company'),
                Section::make('Contact person')
                    ->relationship('companyProfile')
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('contact_first_name')
                                    ->label('First name')
                                    ->maxLength(255),
                                TextInput::make('contact_last_name')
                                    ->label('Last name')
                                    ->maxLength(255),
                                TextInput::make('contact_email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('contact_phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->maxLength(30),
                            ])
                            ->columns(2),
                    ])
                    ->visible(fn (callable $get): bool => $get('account_type') === 'company'),
                Section::make('Assignment & address')
                    ->schema([
                        FormSelect::make('assigned_agent_id')
                            ->label('Assigned agent')
                            ->relationship('assignedAgent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        FormSelect::make('address_id')
                            ->label('Primary address')
                            ->relationship(
                                name: 'address',
                                titleAttribute: 'street',
                                modifyQueryUsing: function ($query, FormSelect $component) {
                                    $user = $component->getRecord();

                                    if (! $user instanceof User || ! $user->getKey()) {
                                        return $query->whereRaw('0 = 1');
                                    }

                                    return $query
                                        ->where(function ($q) use ($user) {
                                            $q->whereHas('users', fn ($sub) => $sub->where('users.id', $user->getKey()));
                                            if ($user->address_id) {
                                                $q->orWhere(
                                                    $q->getModel()->getQualifiedKeyName(),
                                                    $user->address_id
                                                );
                                            }
                                        })
                                        ->orderByDesc($query->qualifyColumn('id'));
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn (Address $record): string => trim(implode(', ', array_filter([
                                $record->street,
                                $record->city,
                                $record->state,
                                $record->country,
                                $record->zip,
                            ]))))
                            ->searchable(['street', 'city', 'state', 'zip', 'country'])
                            ->preload()
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ]);
    }
}
