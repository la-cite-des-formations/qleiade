<?php

namespace App\Filament\Admin\Resources\Indicators;

use App\Filament\Admin\Resources\Indicators\Pages\ManageIndicators;
use Models\Indicator;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IndicatorResource extends Resource
{
    protected static ?string $model = Indicator::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;
    protected static ?string $navigationParentItem = 'Labels Qualité';
    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Indicateurs';

    protected static ?string $modelLabel = 'Indicateur';

    protected static ?string $pluralModelLabel = 'Indicateurs';

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('name', \Illuminate\Support\Str::slug($state))),
                TextInput::make('name')
                    ->maxLength(255)
                    ->label('Identifiant')
                    ->hidden()
                    ->dehydrated()
                    ->required(),
                TextInput::make('number')
                    ->numeric()
                    ->required()
                    ->label('Numéro'),
                Textarea::make('description')
                    ->maxLength(500)
                    ->label('Description')
                    ->rows(3),
                TextInput::make('conformity_level_expected')
                    ->numeric()
                    ->label('Niveau de conformité attendu')
                    ->default(100)
                    ->minValue(0)
                    ->maxValue(100),
                Select::make('criteria_id')
                    ->relationship('criteria', 'label')
                    ->required()
                    ->label('Critère')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('number')
                    ->sortable()
                    ->label('N°'),
                TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->label('Nom'),
                TextColumn::make('criteria.label')
                    ->sortable()
                    ->label('Critère'),
                TextColumn::make('description')
                    ->searchable()
                    ->label('Description')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip(__('filament-actions::edit.single.label')),
                DeleteAction::make()
                    ->icon(Heroicon::OutlinedTrash)
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip(__('filament-actions::delete.single.label')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageIndicators::route('/'),
        ];
    }
}
