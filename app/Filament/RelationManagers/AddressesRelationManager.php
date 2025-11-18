<?php

namespace App\Filament\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Squire\Models\Country;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $recordTitleAttribute = 'street';

    protected static ?string $pluralModelLabel = 'Addresses';
    protected static ?string $modelLabel = 'Address';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('street')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('zip')
                ->required()
                ->maxLength(20),

            Forms\Components\TextInput::make('city')
                ->required()
                ->maxLength(100),

            Forms\Components\TextInput::make('state')
                ->required()
                ->maxLength(100),

            Forms\Components\Select::make('country')
                ->searchable()
                ->getSearchResultsUsing(fn (string $query) =>
                    Country::where('name', 'like', "%{$query}%")
                        ->pluck('name', 'name')
                )
                ->getOptionLabelUsing(fn ($value): ?string =>
                    Country::firstWhere('name', $value)?->name
                ),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('street'),
                Tables\Columns\TextColumn::make('zip'),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\TextColumn::make('country'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Address'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit Address'),

                DeleteAction::make()
                    ->label('Delete'),
            ])
            ->emptyStateHeading('No addresses')
            ->emptyStateDescription('Create an address to get started.')
            ->emptyStateIcon('heroicon-o-map-pin');
    }
}