<?php

namespace App\Filament\Admin\Resources\Wealths;

use App\Filament\Admin\Resources\Wealths\Pages\ManageWealths;
use Models\Wealth;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Indicator as FilterIndicator;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Models\QualityLabel;
use Models\Indicator;
use Models\Unit;

class WealthResource extends Resource
{
    protected static ?string $model = Wealth::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?int $navigationSort = 60;

    protected static ?string $navigationLabel = 'Preuves';

    protected static ?string $modelLabel = 'Preuve';

    protected static ?string $pluralModelLabel = 'Preuves';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom de la preuve'),
                Textarea::make('description')
                    ->maxLength(500)
                    ->label('Description')
                    ->rows(3),
                Select::make('unit_id')
                    ->relationship('unit', 'label')
                    ->required()
                    ->label('Service')
                    ->preload(),
                Select::make('wealth_type_id')
                    ->relationship('wealthType', 'label')
                    ->label('Type de preuve')
                    ->preload(),
                Select::make('indicators')
                    ->relationship('indicators', 'label')
                    ->multiple()
                    ->preload()
                    ->label('Indicateurs'),
                Select::make('actions')
                    ->relationship('actions', 'label')
                    ->multiple()
                    ->preload()
                    ->label('Activités'),
                Select::make('tags')
                    ->relationship('tags', 'label')
                    ->multiple()
                    ->preload()
                    ->label('Libellés'),
                TextInput::make('conformity_level')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(100)
                    ->label('Niveau de conformité (0-100)'),
                DateTimePicker::make('validity_date')
                    ->label('Date de validité'),
                Textarea::make('tracking')
                    ->maxLength(500)
                    ->label('Suivi')
                    ->rows(2),
            ]);
    }

    private static function getTableColumns(): array
    {
        return [
            IconColumn::make('archived_at')
                ->label(new HtmlString(Blade::render('<x-heroicon-o-archive-box style="width: 1.5rem; height: 1.5rem;" />')))
                ->boolean()
                ->getStateUsing(fn (Wealth $record): bool => !is_null($record->archived_at))
                ->trueIcon(Heroicon::OutlinedArchiveBox)
                ->falseIcon(Heroicon::OutlinedMinus)
                ->color(fn (bool $state): string => $state ? 'warning' : 'gray')
                ->sortable()
                ->verticalAlignment('start'),
            TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->label('Nom')
                ->wrap()
                ->verticalAlignment('start'),
            TextColumn::make('unit.name')
                ->sortable()
                ->searchable()
                ->label('Service')
                ->verticalAlignment('start'),
            TextColumn::make('wealthType.label')
                ->sortable()
                ->searchable()
                ->label('Type')
                ->verticalAlignment('start'),
            TextColumn::make('validity_date')
                ->date('d/m/Y')
                ->sortable()
                ->label('Date de validité')
                ->verticalAlignment('start'),
        ];
    }

    private static function getTableFilters(): array
    {
        return [
            SelectFilter::make('unit_id')
                ->label('Service')
                ->relationship(
                    name: 'unit',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'),
                )
                ->getOptionLabelFromRecordUsing(fn (Unit $record): string => $record->full)
                ->multiple()
                ->columnSpan(9),
            SelectFilter::make('wealth_type_id')
                ->label('Type')
                ->relationship('wealthType', 'label')
                ->native(false)
                ->columnSpan(4),
            Filter::make('importance')
                ->schema([
                    Select::make('value')
                        ->options([
                            'essential' => 'Essentielle',
                            'complementary' => 'Complémentaire',
                        ])
                        ->label('Importance')
                        ->placeholder('Tout')
                        ->native(false),
                ])
                ->query(fn (Builder $query, array $data): Builder =>
                    $query
                        ->when(
                            value: $data['value'] === 'essential',
                            callback: fn (Builder $query) =>
                                $query->whereHas(
                                    relation: 'indicators',
                                    callback: fn (Builder $query) =>
                                        $query->where('is_essential', true))
                        )
                        ->when(
                            value: $data['value'] === 'complementary',
                            callback: fn (Builder $query) =>
                                $query->whereHas(
                                    relation: 'indicators',
                                    callback: fn (Builder $query) =>
                                        $query->where('is_essential', false))
                        )
                )
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['value'] ?? null) {
                        $indicators[] = FilterIndicator::make('importance')
                            ->label('Importance: ' . ($data['value'] === 'essential' ? 'Essentielle' : 'Complémentaire'));
                    }

                    return $indicators;
                })
                ->columnSpan(5),
            SelectFilter::make('tags')
                ->label('Libellés')
                ->relationship('tags', 'label')
                ->multiple()
                ->columnSpan(8),
            Filter::make('quality_classification')
                ->columns(13)
                ->columnSpanFull()
                ->schema([
                    Select::make('quality_label_id')
                        ->label('Label Qualité')
                        ->options(QualityLabel::all()->pluck('label', 'id'))
                        ->placeholder('Tout')
                        ->live()
                        ->native(false)
                        ->afterStateUpdated(fn (Set $set) =>
                            $set('indicator_id', null)
                        )
                        ->columnSpan(4),
                    Select::make('indicator_id')
                        ->relationship(
                            name: 'indicators',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query, Get $get) =>
                                $query->whereHas(
                                    relation: 'qualityLabel',
                                    callback: fn (Builder $query) =>
                                        $query->where('quality_label.id', $get('quality_label_id'))
                                )
                                ->orderBy('name')
                        )
                        ->preload()
                        ->label('Indicateur')
                        ->getOptionLabelFromRecordUsing(fn (Indicator $record): string => $record->full)
                        ->placeholder('Tout')
                        ->native(false)
                        ->disabled(fn (Get $get): bool => ! filled($get('quality_label_id')))
                        ->columnSpan(9),
                ])
                ->query(fn (Builder $query, array $data): Builder =>
                    $query
                        ->when(
                            value: $data['quality_label_id'],
                            callback: fn (Builder $query, $value) => $query->whereHas(
                                relation: 'indicators',
                                callback: fn (Builder $query) => $query->whereHas(
                                    relation: 'qualityLabel',
                                    callback: fn (Builder $query) => $query->where('quality_label.id', $value)
                                )
                            )
                        )
                        ->when(
                            value: $data['indicator_id'],
                            callback: fn (Builder $query, $value) => $query->whereHas(
                                relation: 'indicators',
                                callback: fn (Builder $query) => $query->where('indicator.id', $value)
                            )
                        )
                )
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['quality_label_id'] ?? null) {
                        $indicators[] = FilterIndicator::make('quality_label')
                            ->label('Label Qualité: ' . QualityLabel::find($data['quality_label_id'])?->label);
                    }

                    if ($data['indicator_id'] ?? null) {
                        $indicators[] = FilterIndicator::make('indicator')
                            ->label('Indicateur: ' . Indicator::find($data['indicator_id'])?->name)
                            ->removeField('indicator_id');
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
                ->tooltip(__('filament-actions::view.single.label')),
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
            ->recordTitleAttribute('name')
            ->columns(self::getTableColumns())
            ->extraAttributes(['class' => 'resource-table'])
            ->extremePaginationLinks(true)
            ->filters(self::getTableFilters(), layout: FiltersLayout::Dropdown)
            ->filtersFormWidth(Width::ExtraLarge)
            ->filtersFormColumns(13)
            ->deferFilters(false)
            ->recordActions(self::getTableActions())
            ->toolbarActions(self::getTableBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWealths::route('/'),
        ];
    }
}
