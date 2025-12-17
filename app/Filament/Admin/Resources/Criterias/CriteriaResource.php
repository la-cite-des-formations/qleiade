<?php

namespace App\Filament\Admin\Resources\Criterias;

use App\Filament\Admin\Resources\Criterias\Pages\ManageCriterias;
use App\Filament\Admin\Resources\Indicators\IndicatorResource;
use Models\Criteria;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Models\Indicator;

class CriteriaResource extends Resource
{
    protected static ?string $model = Criteria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;
    protected static ?string $navigationParentItem = 'Labels Qualité';
    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Critères';

    protected static ?string $modelLabel = 'Critère';

    protected static ?string $pluralModelLabel = 'Critères';

    protected static ?string $recordTitleAttribute = 'label';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description')
                    ->hiddenLabel()
                    ->columnSpanFull(),
                TextEntry::make('indicators')
                    ->label(fn(Criteria $criteria): string => "{$criteria->indicators->count()} indicateurs :")
                    ->state(
                        fn(Criteria $criteria): string => $criteria->indicators
                            ->sortBy('number', SORT_NATURAL)
                            ->map(fn(Indicator $indicator) => "{$indicator->number} - {$indicator->label}")
                            ->join('<br>')
                    )
                    ->html()
                    ->columnSpanFull(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quality_label_id')
                    ->required()
                    ->relationship('qualityLabel', 'label')
                    ->label('Label Qualité')
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, string $operation) {
                        if ($operation !== 'create' || ! $state) {
                            return;
                        }

                        $maxOrder = Criteria::where('quality_label_id', $state)->max('order');
                        $newOrder = $maxOrder + 1;

                        $set('order', $newOrder);
                        $set('label', "Critère $newOrder");
                        $set('name', Str::slug("Critère $newOrder"));
                    })
                    ->placeholder('Choisir...'),
                TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom')
                    ->columnStart(1),
                Textarea::make('description')
                    ->maxLength(1500)
                    ->label('Description')
                    ->rows(5)
                    ->autofocus(fn ($get, string $operation) => $operation === 'create' && filled($get('quality_label_id')))
                    ->columnSpanFull(),
                TextInput::make('order')
                    ->numeric()
                    ->minValue(1)
                    ->label('Ordre'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('qualityLabel.label')
                    ->sortable()
                    ->label('Label Qualité')
                    ->verticalAlignment('start'),
                TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->label('Critère')
                    ->verticalAlignment('start'),
                TextColumn::make('description')
                    ->searchable()
                    ->label('Description')
                    ->wrap(),
                TextColumn::make('indicators_count')
                    ->label('Indicateurs')
                    ->alignRight()
                    ->verticalAlignment('start')
                    ->url(fn(Criteria $record): string => IndicatorResource::getUrl('index', [
                        'filters' => [
                            'structure' => [
                                'quality_label_id' => $record->quality_label_id,
                                'criteria_id' => $record->id,
                            ],
                        ],
                    ])),
            ])
            ->filters(
                [
                    SelectFilter::make('quality_label_id')
                        ->relationship('qualityLabel', 'label')
                        ->label('Label Qualité'),
                ], layout: FiltersLayout::AboveContent
            )
            ->deferFilters(false)
            ->recordActions([
                ViewAction::make()
                    ->icon(Heroicon::Eye)
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip(__('filament-actions::view.single.label'))
                    ->modalHeading(fn(Criteria $criteria): string => "{$criteria->qualityLabel->label} - {$criteria->label}"),
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
            ])
            ->extraAttributes([
                'class' => 'resource-table',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCriterias::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('indicators');
    }
}
