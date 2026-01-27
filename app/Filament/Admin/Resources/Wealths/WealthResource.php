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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Filament\Infolists\Components\TextEntry;
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
use Filament\Schemas\Components\Tabs;
use App\Filament\Components\Tab;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\IconSize;
use Models\WealthType;

class WealthResource extends Resource
{
    protected static ?string $model = Wealth::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?int $navigationSort = 60;

    protected static ?string $navigationLabel = 'Preuves';

    protected static ?string $modelLabel = 'Preuve';

    protected static ?string $pluralModelLabel = 'Preuves';

    protected static ?string $recordTitleAttribute = 'name';

    private static function getIdentityTabSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nom')
                ->placeholder("Nom explicite de la preuve (Ex. 'Attestation de formation')")
                ->required()
                ->maxLength(255)
                ->columnSpan(3),
            DatePicker::make('validity_date')
                ->label('Date de validité')
                ->placeholder('JJ/MM/AAAA')
                ->displayFormat('d/m/Y')
                ->native(false),
            RichEditor::make('description')
                ->label('Description')
                ->placeholder('Description détaillée de la preuve')
                ->required()
                ->hintIcon(Heroicon::OutlinedInformationCircle, "Important pour l'audit ! Soyez le plus précis possible s'il vous plait.")
                ->extraInputAttributes(['style' => 'min-height: 20em;'])
                ->columnSpanFull(),
        ];
    }

    private static function getTypeTabSchema(): array
    {
        return [
            Select::make('wealth_type_id')
                ->relationship('wealthType', 'label')
                ->label('Type de preuve')
                ->placeholder('Choisir...')
                ->required()
                ->live()
                ->native(false)
                ->default(WealthType::firstWhere('name', 'link')->id)
                ->columnSpan(1),
            Group::make()
                ->schema(function (Get $get) {
                    $wealthTypeId = $get('wealth_type_id');
                    if (! $wealthTypeId) {
                        return [];
                    }
                    $wealthType = WealthType::find($wealthTypeId);
                    if (! $wealthType) {
                        return [];
                    }

                    return match ($wealthType->name) {
                        'file' => [
                            FileUpload::make('attachment_file')
                                ->label('Fichier')
                                ->multiple()
                                ->directory('wealths')
                                ->required()
                                ->columnSpan(2),
                        ],
                        'link' => [
                            Select::make('attachment_link')
                                ->label('Type de lien')
                                ->placeholder('Choisir...')
                                ->options([
                                    'web' => 'Site Web',
                                    'google' => 'Répertoire Google',
                                    'autre' => 'Autre',
                                ])
                                ->required()
                                ->native(false)
                                ->columnSpan(1),
                            TextInput::make('url')
                                ->url()
                                ->required()
                                ->columnSpan(3),
                        ],
                        'ypareo' => [
                            RichEditor::make('attachment_ypareo')
                                ->label('Processus Ypareo')
                                ->placeholder('Description détaillée du mode opératoire du processus Ypareo')
                                ->required()
                                ->extraInputAttributes(['style' => 'min-height: 20em;'])
                                ->columnSpanFull(),
                        ],
                    };
                })
                ->columns(4)
                ->columnSpanFull()
                ->key('dynamic_attachment_group'),
        ];
    }

    private static function getQualificationTabSchema(): array
    {
        return [
            Select::make('unit_id')
                ->relationship('unit', 'label')
                ->required()
                ->label('Service')
                ->preload()
                ->searchable(),
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
            Fieldset::make('Granularité')
                ->schema([
                    Select::make('granularity.type')
                        ->label('Type')
                        ->options([
                            'global' => 'Global',
                            'formation' => 'Formation',
                            'student' => 'Apprenant',
                        ])
                        ->default('global')
                        ->required()
                        ->live(),
                    Select::make('granularity.id')
                        ->label('Formation')
                        ->options(function() {
                            try {
                                $schoolManager = app(\School\Manager\SchoolManager::class);
                                $formations = $schoolManager->getFormations();
                                
                                $resolved = $formations->resolve();
                                $data = isset($resolved['data']) && method_exists($resolved['data'], 'resolve') 
                                    ? $resolved['data']->resolve() 
                                    : [];
                                
                                return collect($data)
                                    ->filter(fn($item) => !empty($item['id']) && !empty($item['label']))
                                    ->mapWithKeys(function ($item) {
                                        $code = filled($item['code'] ?? null) ? "[{$item['code']}] " : "";
                                        return [$item['id'] => $code . $item['label']];
                                    });
                            } catch (\Exception $e) {
                                return [];
                            }
                        })
                        ->searchable()
                        ->preload()
                        ->visible(fn (Get $get) => $get('granularity.type') === 'formation'),
                    TextInput::make('granularity.id')
                        ->label('ID Apprenant')
                        ->visible(fn (Get $get) => $get('granularity.type') === 'student'),
                ]),
        ];
    }

    private static function getIndicatorsTabSchema(): array
    {
        return [
            Tabs::make('Qualite')
                ->contained(false)
                ->tabs(fn () => QualityLabel::with('criterias.indicators')->get()->map(function ($qualityLabel) {
                    return Tab::make($qualityLabel->label)
                        ->schema([
                            Tabs::make('Criteres')
                                ->contained(false)
                                ->extraAttributes(['style' => 'margin-top: -20px;'])
                                ->tabs($qualityLabel->criterias->map(function ($criteria) {
                                    return Tab::make($criteria->label)
                                        ->schema([
                                            TextEntry::make('description')
                                                ->hiddenLabel()
                                                ->state($criteria->description)
                                                ->extraAttributes([
                                                    'style' => 'font-size: 0.875rem; font-style: italic; margin-bottom: 1rem;',
                                                ])
                                                ->hidden(fn() => empty($criteria->description)),
                                            
                                            Grid::make(12)
                                                ->extraAttributes(['class' => 'gap-y-0'])
                                                ->schema($criteria->indicators->flatMap(function ($indicator) {
                                                    return [
                                                        Checkbox::make("indicators_matrix.{$indicator->id}.checked")
                                                            ->label($indicator->full)
                                                            ->columnSpan(9)
                                                            ->live(),
                                                        Toggle::make("indicators_matrix.{$indicator->id}.is_essential")
                                                            ->label('Essentielle')
                                                            ->onColor('success')
                                                            ->offColor('gray')
                                                            ->inline(true)
                                                            ->columnSpan(3)
                                                            ->extraAttributes([
                                                                'style' => 'transform: scale(0.6); transform-origin: right;',
                                                            ])
                                                            ->disabled(fn (Get $get) => ! $get("indicators_matrix.{$indicator->id}.checked")),
                                                    ];
                                                })->all()),
                                        ]);
                                })->all()),
                        ]);
                })->all()),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Identité')
                            ->schema(self::getIdentityTabSchema())
                            ->columns(4),
                        Tab::make('Type')
                            ->schema(self::getTypeTabSchema())
                            ->columns(4),
                        Tab::make('Qualification')
                            ->schema(self::getQualificationTabSchema())
                            ->columns(2),
                        Tab::make('Indicateurs')
                            ->schema(self::getIndicatorsTabSchema()),
                    ])
                    ->columnSpanFull()
                    ->extraAttributes(['style' => 'min-height: 75vh;']),
            ]);
    }

    private static function getTableColumns(): array
    {
        return [
            IconColumn::make('status')
                ->label('État')
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
                    default => null,
                })
                ->size(IconSize::Medium)
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query
                        ->orderBy('archived_at', $direction)
                        ->orderBy('validity_date', $direction);
                })
                ->verticalAlignment('start'),
            TextColumn::make('name')
                ->label('Nom')
                ->sortable()
                ->searchable()
                ->wrap()
                ->verticalAlignment('start'),
            TextColumn::make('unit.name')
                ->label('Service')
                ->sortable()
                ->searchable()
                ->verticalAlignment('start'),
            TextColumn::make('wealthType.label')
                ->label('Type')
                ->sortable()
                ->searchable()
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
                            ->label('Label Qualité: ' . QualityLabel::find($data['quality_label_id'], ['*'])?->label);
                    }

                    if ($data['indicator_id'] ?? null) {
                        $indicators[] = FilterIndicator::make('indicator')
                            ->label('Indicateur: ' . Indicator::find($data['indicator_id'], ['*'])?->name)
                            ->removeField('indicator_id');
                    }

                    return $indicators;
                }),
        ];
    }

    public static function prepareDataForForm(array $data, Wealth $record): array
    {
        if ($record->wealthType) {
            $data['attachment_' . $record->wealthType->name] = $record->attachment;
        }

        $data['indicators_matrix'] = [];
        foreach ($record->indicators as $indicator) {
            $data['indicators_matrix'][$indicator->id] = [
                'checked' => true,
                'is_essential' => (bool) $indicator->pivot->is_essential,
            ];
        }

        return $data;
    }

    public static function saveRelationships(Wealth $record, array $data): Wealth
    {
        // 1. Préparer les données du modèle
        $modelFields = $record->getFillable();
        $record->fill(collect($data)->only($modelFields)->toArray());

        // 2. Gérer l'attachement (pièce jointe) spécifique
        $type = WealthType::find($data['wealth_type_id'] ?? null, ['*']);
        if ($type) {
            $key = 'attachment_' . $type->name;
            if (isset($data[$key])) {
                $record->attachment = $data[$key];
            }
        }

        // 3. Sauvegarder
        $record->save();

        // 4. Synchroniser les indicateurs
        $indicatorsMatrix = $data['indicators_matrix'] ?? [];
        $syncData = [];
        foreach ($indicatorsMatrix as $id => $settings) {
            if ($settings['checked'] ?? false) {
                $syncData[$id] = ['is_essential' => $settings['is_essential'] ?? false];
            }
        }
        $record->indicators()->sync($syncData);

        return $record;
    }

    private static function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::Eye)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::view.single.label'))
                ->slideOver(),
            EditAction::make()
                ->icon(Heroicon::OutlinedPencilSquare)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::edit.single.label'))
                ->slideOver()
                ->mutateRecordDataUsing(fn (array $data, Wealth $record): array => self::prepareDataForForm($data, $record))
                ->using(fn (Wealth $record, array $data): Wealth => self::saveRelationships($record, $data)),
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
