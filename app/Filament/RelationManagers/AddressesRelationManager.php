<?php

namespace App\Filament\RelationManagers;

use App\Models\Address;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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
            Section::make('Address Details')
                ->icon('heroicon-o-map-pin')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('street')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->prefixIcon('heroicon-o-home'),

                    Forms\Components\TextInput::make('city')
                        ->required()
                        ->maxLength(100)
                        ->prefixIcon('heroicon-o-building-office-2'),

                    Forms\Components\TextInput::make('state')
                        ->required()
                        ->maxLength(100)
                        ->prefixIcon('heroicon-o-map'),

                    Forms\Components\TextInput::make('zip')
                        ->label('ZIP Code')
                        ->required()
                        ->maxLength(20)
                        ->prefixIcon('heroicon-o-hashtag'),

                    Forms\Components\Select::make('country')
                        ->searchable()
                        ->prefixIcon('heroicon-o-globe-alt')
                        ->getSearchResultsUsing(fn(string $query) => Country::where('name', 'like', "%{$query}%")
                            ->pluck('name', 'name')
                        )
                        ->getOptionLabelUsing(fn($value): ?string => Country::firstWhere('name', $value)?->name),
                ]),

            Section::make('Notes')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->collapsible()
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->hiddenLabel()
                        ->rows(4)
                        ->placeholder('Any additional notes about this address...'),
                ]),
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
                    ->label('Add Address')
                    ->modalWidth('7xl'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->modalWidth('7xl'),

                EditAction::make()
                    ->label('Edit')
                    ->modalWidth('7xl'),

                DeleteAction::make()
                    ->label('Delete'),
            ])
            ->emptyStateHeading('No addresses')
            ->emptyStateDescription('Create an address to get started.')
            ->emptyStateIcon('heroicon-o-map-pin');
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Address Details')
                ->icon('heroicon-o-map-pin')
                ->columns(2)
                ->schema([
                    TextEntry::make('street')
                        ->label('Street')
                        ->icon('heroicon-o-home')
                        ->columnSpanFull()
                        ->placeholder('—')
                        ->copyable(),
                    TextEntry::make('city')
                        ->icon('heroicon-o-building-office-2')
                        ->placeholder('—'),
                    TextEntry::make('state')
                        ->icon('heroicon-o-map')
                        ->placeholder('—'),
                    TextEntry::make('zip')
                        ->label('ZIP Code')
                        ->icon('heroicon-o-hashtag')
                        ->placeholder('—'),
                    TextEntry::make('country')
                        ->icon('heroicon-o-globe-alt')
                        ->placeholder('—'),
                ]),
            Section::make('Notes')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')
                        ->hiddenLabel()
                        ->placeholder('No notes.')
                        ->prose(),
                ]),
        ]);
    }
}