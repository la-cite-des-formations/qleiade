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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\Indicator as FilterIndicator;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Models\Criteria;
use Models\QualityLabel;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Enums\IconSize;
use Models\Wealth;
use Filament\Actions\ViewAction;

class IndicatorResource extends Resource
{
    protected static ?string $model = Indicator::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;
    protected static string|\UnitEnum|null $navigationGroup = 'RÉFÉRENTIEL';
    protected static ?string $navigationParentItem = 'Labels Qualité';
    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Indicateurs';

    protected static ?string $modelLabel = 'Indicateur';

    protected static ?string $pluralModelLabel = 'Indicateurs';

    protected static ?string $recordTitleAttribute = 'label';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('qualityLabel.label')
                    ->label('Label Qualité'),
                RepeatableEntry::make('wealths')
                    ->state(fn (Indicator $record) => $record->wealths->sortBy([
                        ['pivot.is_essential', 'desc'],
                        [fn ($w) => $w->archived_at ? 2 : ($w->validity_date ? 1 : 0), 'asc'],
                    ]))
                    ->label('Preuves associées')
                    ->table([
                        TableColumn::make('Preuve'),
                        TableColumn::make('Importance'),
                        TableColumn::make('État'),
                    ])
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('pivot.is_essential')
                            ->state(fn ($record) => $record->pivot->is_essential ? 'Essentielle' : 'Complémentaire')
                            ->badge()
                            ->color(fn ($state) => $state === 'Essentielle' ? 'success' : 'gray'),
                        IconEntry::make('status')
                            ->getStateUsing(fn (Wealth $record): Wealth => $record)
                            ->icon(fn (Wealth $state) => match (true) {
                                ! is_null($state->archived_at) => Heroicon::OutlinedArchiveBox,
                                ! is_null($state->validity_date) => Heroicon::OutlinedClock,
                                default => Heroicon::OutlinedMinus,
                            })
                            ->color(fn (Wealth $state) => match (true) {
                                ! is_null($state->archived_at) => 'danger',
                                ! is_null($state->validity_date) => 'warning',
                                default => 'gray',
                            })
                            ->tooltip(fn (Wealth $state) => match (true) {
                                ! is_null($state->archived_at) => 'Archivée le ' . $state->archived_at->format('d/m/Y'),
                                ! is_null($state->validity_date) => 'Valide jusqu\'au ' . $state->validity_date->format('d/m/Y'),
                                default => null
                            }),
                    ])
                    ->visible(fn(Indicator $record) => $record->wealths->isNotEmpty())
                    ->columnSpanFull(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quality_label_id')
                    ->label('Label Qualité')
                    ->relationship('qualityLabel', 'label')
                    ->required()
                    ->placeholder('Choisir...')
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('criteria_id', null);
                        $set('number', null);
                    }),
                Select::make('criteria_id')
                    ->label('Critère')
                    ->relationship(
                        'criteria',
                        'label',
                        fn (Builder $query, Get $get) =>
                            $query->where('quality_label_id', $get('quality_label_id'))
                    )
                    ->required()
                    ->placeholder('Choisir...')
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $lastNumber = Indicator::where('criteria_id', $state)->max('number');
                            $next = $lastNumber ? intval($lastNumber) + 1 : 1;
                            $set('number', str_pad($next, 2, '0', STR_PAD_LEFT));
                        }
                        else {
                            $set('number', null);
                        }
                    })
                    ->visible(fn (Get $get) => filled($get('quality_label_id'))),
                Textarea::make('label')
                    ->required()
                    ->maxLength(191)
                    ->label('Nom')
                    ->rows(2)
                    ->autofocus(fn (Get $get, string $operation) => $operation === 'create' && filled($get('quality_label_id')) && filled($get('criteria_id')))
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->maxLength(1500)
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull(),
                TextInput::make('number')
                    ->required()
                    ->label('Numéro'),
            ]);
    }

    private static function getTableColumns(): array
    {
        return [
            TextColumn::make('qualityLabel.label')
                ->label('Label Qualité')
                ->verticalAlignment('start')
                ->hidden(fn ($livewire) => filled($livewire->getTableFilterState('quality_classification')['quality_label_id'] ?? null)),
            TextColumn::make('criteria.label')
                ->sortable()
                ->label('Critère')
                ->verticalAlignment('start'),
            TextColumn::make('number')
                ->sortable()
                ->label('N°')
                ->verticalAlignment('start'),
            TextColumn::make('label')
                ->searchable()
                ->sortable()
                ->label('Nom')
                ->verticalAlignment('start')
                ->wrap(),
        ];
    }

    private static function getTableFilters(): array
    {
        return [
            Filter::make('quality_classification')
                ->schema([
                    Select::make('quality_label_id')
                        ->relationship('qualityLabel', 'label')
                        ->label('Label Qualité')
                        ->placeholder('Tout')
                        ->default(QualityLabel::count() === 1 ? QualityLabel::first()->id : null)
                        ->live()
                        ->afterStateUpdated(fn (Set $set) =>
                            $set('criteria_id', null)
                        )
                        ->native(false),
                    Select::make('criteria_id')
                        ->relationship(
                            name: 'criteria',
                            titleAttribute: 'label',
                            modifyQueryUsing: fn (Builder $query, Get $get) =>
                                $query->where('quality_label_id', $get('quality_label_id'))
                        )
                        ->preload()
                        ->label('Critère')
                        ->placeholder('Tout')
                        ->disabled(fn (Get $get): bool => ! filled($get('quality_label_id')))
                        ->native(false),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            value: $data['quality_label_id'],
                            callback: fn (Builder $query, $value): Builder =>
                                $query->whereHas(
                                    relation: 'criteria',
                                    callback: fn (Builder $query) =>
                                        $query->where('quality_label_id', $value)
                                ),
                        )
                        ->when(
                            value: $data['criteria_id'],
                            callback: fn (Builder $query, $value): Builder =>
                                $query->where('criteria_id', $value),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    
                    if ($data['quality_label_id'] ?? null) {
                        $indicators[] = FilterIndicator::make('quality_label')
                            ->label('Label Qualité: ' . QualityLabel::find($data['quality_label_id'])?->label);
                    }
                    
                    if ($data['criteria_id'] ?? null) {
                        $indicators[] = FilterIndicator::make('criteria')
                            ->label('Critère: ' . Criteria::find($data['criteria_id'])?->label)
                            ->removeField('criteria_id');
                    }
                    
                    return $indicators;
                }),
        ];
    }

    private static function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::Eye)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::view.single.label'))
                ->slideOver()
                ->modalHeading(fn(Indicator $record): string => $record->full),
            EditAction::make()
                ->fillForm(function (Indicator $record): array {
                    $record->quality_label_id = $record->criteria->quality_label_id;
                    
                    return $record->toArray();
                })
                ->icon(Heroicon::OutlinedPencilSquare)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::edit.single.label'))
                ->slideOver()
                ->modalHeading(fn(Indicator $indicator): string => "Modifier Indicateur {$indicator->number} ({$indicator->qualityLabel->label} - {$indicator->criteria->label})"),
            DeleteAction::make()
                ->icon(Heroicon::OutlinedTrash)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::delete.single.label')),
        ];
    }

    private static function getTableBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns(self::getTableColumns())
            ->extraAttributes(['class' => 'resource-table'])
            ->filters(self::getTableFilters(), layout: FiltersLayout::Dropdown)
            ->deferFilters(false)
            ->recordActions(self::getTableActions())
            ->toolbarActions(self::getTableBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageIndicators::route('/'),
        ];
    }
}
