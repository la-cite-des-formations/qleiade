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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Models\Criteria;
use Models\QualityLabel;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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
                    ->searchable()
                    ->required()
                    ->placeholder('Choisir...')
                    ->preload()
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
                TextInput::make('label')
                    ->required()
                    ->maxLength(191)
                    ->label('Nom')
                    ->autofocus(fn (Get $get, string $operation) => $operation === 'create' && filled($get('quality_label_id')) && filled($get('criteria_id')))
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->maxLength(1500)
                    ->label('Description')
                    ->rows(5)
                    ->columnSpanFull(),
                TextInput::make('number')
                    ->required()
                    ->label('Numéro'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('qualityLabel.label')
                    ->label('Label Qualité')
                    ->verticalAlignment('start'),
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
                    ->wrap(),
            ])
            ->filters(
                [
                    Filter::make('structure')
                        ->schema([
                            Select::make('quality_label_id')
                                ->relationship('qualityLabel', 'label')
                                ->label('Label Qualité')
                                ->preload()
                                ->placeholder('Tout')
                                ->live()
                                ->afterStateUpdated(fn (Set $set, $state) => !$state ? $set('criteria_id', null) : null),
                            Select::make('criteria_id')
                                ->relationship('criteria', 'label', function (Builder $query, Get $get) {
                                    $qualityLabelId = $get('quality_label_id');
                                    if (! $qualityLabelId) {
                                        return $query->whereRaw('1 = 0'); // Liste vide si pas de label
                                    }
                                    return $query->where('quality_label_id', $qualityLabelId);
                                })
                                ->label('Critère')
                                ->preload()
                                ->placeholder('Tout')
                                ->visible(fn (Get $get) => filled($get('quality_label_id'))),
                        ])
                        ->columns(2)
                        ->columnSpan(2)
                        ->query(function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['quality_label_id'],
                                    fn (Builder $query, $value): Builder => $query->whereHas('criteria', fn (Builder $query) => $query->where('quality_label_id', $value)),
                                )
                                ->when(
                                    $data['criteria_id'],
                                    fn (Builder $query, $value): Builder => $query->where('criteria_id', $value),
                                );
                        })
                        ->indicateUsing(function (array $data): array {
                            $indicators = [];
                            if ($data['quality_label_id'] ?? null) {
                                $indicators[] = 'Label Qualité: ' . QualityLabel::find($data['quality_label_id'])?->label;
                            }
                            if ($data['criteria_id'] ?? null) {
                                $indicators[] = 'Critère: ' . Criteria::find($data['criteria_id'])?->label;
                            }
                            return $indicators;
                        }),
                ], layout: FiltersLayout::AboveContent
            )
            ->deferFilters(false)
            ->recordActions([
                EditAction::make()
                    ->fillForm(function (Indicator $record): array {
                        $record->quality_label_id = $record->criteria->quality_label_id;
                        
                        return $record->toArray();
                    })
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
            'index' => ManageIndicators::route('/'),
        ];
    }
}
