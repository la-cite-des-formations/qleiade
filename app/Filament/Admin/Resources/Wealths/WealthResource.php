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
use Filament\Support\Enums\Width;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Indicator as FilterIndicator;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Models\QualityLabel;
use Models\Indicator;
use Models\Unit;
use Filament\Schemas\Components\Tabs;
use App\Filament\Components\Tab;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\IconSize;
use Models\WealthType;
use School\Manager\SchoolManager;

class WealthResource extends Resource
{
    protected static ?string $model = Wealth::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static string|\UnitEnum|null $navigationGroup = 'DONNÉES';
    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Preuves';

    protected static ?string $modelLabel = 'Preuve';

    protected static ?string $pluralModelLabel = 'Preuves';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getWidgetDescription(): string
    {
        return 'Centraliser les preuves et documents justificatifs.';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description')
                    ->label('Description')
                    ->hiddenLabel()
                    ->html()
                    ->columnSpanFull(),

                Grid::make(2)
                    ->schema([
                        TextEntry::make('unit.label')
                            ->label('Service'),
                        TextEntry::make('status')
                            ->label('État')
                            ->getStateUsing(fn (Wealth $record): string => match (true) {
                                ! is_null($record->archived_at) => 'Archivée',
                                ! is_null($record->validity_date) => 'Temporaire',
                                default => 'Active',
                            })
                            ->badge()
                            ->color(fn (Wealth $record) => match (true) {
                                ! is_null($record->archived_at) => 'danger',
                                ! is_null($record->validity_date) => 'warning',
                                default => 'success',
                            })
                            ->icon(fn (Wealth $record) => match (true) {
                                ! is_null($record->archived_at) => Heroicon::OutlinedArchiveBox,
                                ! is_null($record->validity_date) => Heroicon::OutlinedClock,
                                default => Heroicon::OutlinedCheckCircle,
                            })
                            ->tooltip(fn (Wealth $record) => match (true) {
                                ! is_null($record->archived_at) => 'Archivée le ' . \Carbon\Carbon::parse($record->archived_at)->format('d/m/Y'),
                                ! is_null($record->validity_date) => 'Valide jusqu\'au ' . \Carbon\Carbon::parse($record->validity_date)->format('d/m/Y'),
                                default => null,
                            }),
                    ])
                    ->columnSpanFull(),

                Group::make([
                    TextEntry::make('wealthType.label')
                        ->label('Type')
                        ->formatStateUsing(fn ($state) => "$state :")
                        ->columnSpanFull(),

                    TextEntry::make('attachment_detail')
                        ->hiddenLabel()
                        ->html()
                        ->state(function (Wealth $record) {
                            $type = $record->wealthType?->name;
                            $attachment = $record->attachment;

                            if (! $type || empty($attachment[$type])) {
                                return null;
                            }

                            $data = $attachment[$type];

                            return match ($type) {
                                'link' => $data['url'] 
                                    ? new HtmlString(
                                        Blade::render(
                                            '<x-filament::link :href="$url" target="_blank" color="primary">{{ $url }}</x-filament::link>',
                                            [
                                                'url' => $data['url'],
                                            ]
                                        )
                                    ) 
                                    : null,
                                'ypareo' => new HtmlString($data['process'] ?? ''),
                                default => null,
                            };
                        })
                        ->visible(fn (Wealth $record) => in_array($record->wealthType?->name, ['link', 'ypareo']))
                        ->columnSpanFull()
                        ->extraAttributes(['style' => 'margin-top: -1rem;']),
                ])
                ->columnSpanFull()
                ->extraAttributes(['class' => 'space-y-0']),

                TextEntry::make('granularity_unified')
                    ->label('Granularité')
                    ->state(function (Wealth $record) {
                        $granularity = $record->granularity;
                        $type = $granularity['type'] ?? 'global';
                        $id = $granularity['id'] ?? null;

                        $typeLabel = match ($type) {
                            'global' => 'Global',
                            'formation' => 'Formation',
                            'student' => 'Apprenant',
                            default => ucfirst($type),
                        };

                        if ($type === 'global' || ! $id) {
                            return $typeLabel;
                        }

                        return "{$typeLabel} ({$id})";
                    })
                    ->columnSpanFull(),

                RepeatableEntry::make('indicators')
                    ->label('Indicateurs certifiés')
                    ->table([
                        TableColumn::make('Label Qualité'),
                        TableColumn::make('Indicateurs'),
                        TableColumn::make('Importance'),
                    ])
                    ->schema([
                        TextEntry::make('qualityLabel.label'),
                        TextEntry::make('full'),
                        TextEntry::make('pivot.is_essential')
                            ->state(fn ($record) => $record->pivot->is_essential ? 'Essentielle' : 'Complémentaire')
                            ->badge()
                            ->color(fn ($state) => $state === 'Essentielle' ? 'success' : 'gray'),
                    ])
                    ->columnSpanFull()
                    ->visible(fn($record) => $record->indicators->isNotEmpty()),

                TextEntry::make('actions.label')
                    ->label('Activités associées')
                    ->badge()
                    ->color('info')
                    ->visible(fn($record) => $record->actions->isNotEmpty())
                    ->columnSpanFull(),

                TextEntry::make('tags.label')
                    ->label('Libellés associés')
                    ->badge()
                    ->color('gray')
                    ->visible(fn($record) => $record->tags->isNotEmpty())
                    ->columnSpanFull(),
            ]);
    }

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
                ->default(WealthType::firstWhere('name', 'link')->id)
                ->live()
                ->native(false)
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
                                ->placeholder('Url complet du lien (Ex. https://www.lapreuve.com)')
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
                ->label('Service')
                ->relationship('unit', 'label')
                ->required()
                ->placeholder('Choisir...')
                ->native(false),
            Fieldset::make('Granularité')
                ->schema([
                    Select::make('granularity.type')
                        ->label('Type')
                        ->placeholder('Choisir...')
                        ->options([
                            'global' => 'Global',
                            'formation' => 'Formation',
                            'student' => 'Apprenant',
                        ])
                        ->default('global')
                        ->required()
                        ->live()
                        ->native(false),
                    Select::make('granularity.id')
                        ->label('Formation')
                        ->options(function() {
                            try {
                                $schoolManager = app(SchoolManager::class);
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
                        ->optionsLimit(1000)
                        ->required(fn (Get $get) => $get('granularity.type') === 'formation')
                        ->placeholder('Choisir...')
                        ->searchable()
                        ->preload()
                        ->visible(fn (Get $get) => $get('granularity.type') === 'formation')
                        ->columnSpan(2),
                    Select::make('granularity.id')
                        ->label('Apprenant')
                        ->options(function() {
                            try {
                                $schoolManager = app(SchoolManager::class);
                                
                                // On récupère les périodes en cours
                                $periods = collect($schoolManager->getPeriods(null, null, true));
                                $periodIds = $periods->pluck('codePeriode')->toArray();
                                
                                if (empty($periodIds)) {
                                    return [];
                                }
                                
                                $connecter = $schoolManager->connecter('ypareo');
                                $allStudents = collect();
                                
                                foreach ($periodIds as $periodId) {
                                    $rawResponse = $connecter->apprenants('FI', $periodId);
                                    
                                    // Utiliser le casting en string car getContents() peut être vidé après une première lecture
                                    $content = (string) $rawResponse;
                                    $data = json_decode($content, true);
                                    
                                    $students = $data['data'] ?? $data;

                                    if ($students && is_array($students)) {
                                        $allStudents = $allStudents->concat($students);
                                    }
                                }
                                
                                $options = $allStudents
                                    ->unique('codeApprenant')
                                    ->mapWithKeys(function ($item) {
                                        $fullName = ($item['nomApprenant'] ?? '') . " " . ($item['prenomApprenant'] ?? '');
                                        return [$item['codeApprenant'] => trim($fullName)];
                                    })
                                    ->filter()
                                    ->sort()
                                    ->toArray();

                                return $options;
                            }
                            catch (\Exception $e) {
                                return [];
                            }
                        })
                        ->optionsLimit(1000)
                        ->required(fn (Get $get) => $get('granularity.type') === 'student')
                        ->placeholder('Choisir...')
                        ->searchable()
                        ->preload()
                        ->visible(fn (Get $get) => $get('granularity.type') === 'student')
                        ->columnSpan(2),
                ])
                ->columns(4)
                ->columnSpanFull(),
            Select::make('actions')
                ->label('Activités')
                ->relationship('actions', 'label')
                ->multiple()
                ->placeholder('Choisir...')
                ->searchable(false)
                ->native(false),
            Select::make('tags')
                ->label('Libellés')
                ->relationship('tags', 'label')
                ->multiple()
                ->placeholder('Choisir...')
                ->searchable(false)
                ->native(false),
        ];
    }

    private static function getIndicatorsTabSchema(): array
    {
        return [
            Tabs::make('Qualite')
                ->tabs(fn () => QualityLabel::with('criterias.indicators')->get()->map(function ($qualityLabel) {
                    return Tab::make($qualityLabel->label)
                        ->schema([
                            Tabs::make('Criteres')
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
                                })->all())
                                ->extraAttributes([
                                    'class' => 'flat-tabs',
                                    'style' => 'min-height: 75vh;',
                                ]),
                        ]);
                })->all())
                ->vertical()
                ->extraAttributes(['class' => 'nested-tabs flat-tabs']),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->extraAttributes(['class' => 'flat-tabs'])
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
                            ->schema(self::getIndicatorsTabSchema())
                            ->extraAttributes(['class' => 'nested-tabs']),
                    ])
                    ->columnSpanFull(),
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
        $attachment = $record->attachment;
        if ($record->wealthType && !empty($attachment[$record->wealthType->name])) {
            $typeData = $attachment[$record->wealthType->name];
            
            if ($record->wealthType->name === 'link') {
                $data['attachment_link'] = $typeData['type'] ?? null;
                $data['url'] = $typeData['url'] ?? null;
            }
            elseif ($record->wealthType->name === 'ypareo') {
                $data['attachment_ypareo'] = $typeData['process'] ?? null;
            }
            elseif ($record->wealthType->name === 'file') {
                $data['attachment_file'] = $typeData; // FileUpload handles array/null
            }
        }

        $data['indicators_matrix'] = [];
        foreach ($record->indicators as $indicator) {
            $data['indicators_matrix'][$indicator->id] = [
                'checked' => true,
                'is_essential' => (bool) $indicator->pivot->is_essential,
            ];
        }

        if (isset($data['granularity']['id']) && is_array($data['granularity']['id'])) {
            $data['granularity']['id'] = head($data['granularity']['id']);
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
            $attachmentData = [];
            
            if ($type->name === 'link') {
                $attachmentData = [
                    'link' => [
                        'type' => $data['attachment_link'] ?? null,
                        'url' => $data['url'] ?? null,
                    ]
                ];
            } elseif ($type->name === 'ypareo') {
                $attachmentData = [
                    'ypareo' => [
                        'process' => $data['attachment_ypareo'] ?? null,
                    ]
                ];
            } elseif ($type->name === 'file') {
                $attachmentData = [
                    'file' => $data['attachment_file'] ?? [],
                ];
            }
            
            if (!empty($attachmentData)) {
                $record->attachment = $attachmentData;
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
                ->modalHeading(fn (Wealth $record) => $record->name)
                ->slideOver(),
            EditAction::make()
                ->icon(Heroicon::OutlinedPencilSquare)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::edit.single.label'))
                ->extraModalWindowAttributes(['class' => 'modal-no-padding'])
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
