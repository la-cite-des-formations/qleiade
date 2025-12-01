<?php

namespace App\Filament\Resources\Indicators;

use App\Filament\Resources\Indicators\Pages\ManageIndicators;
use Models\Indicator;
use Models\QualityLabel;
use Models\Criteria;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IndicatorResource extends Resource
{
    protected static ?string $model = Indicator::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpCircle;

    protected static ?string $modelLabel = 'indicateur';
    protected static ?string $pluralModelLabel = 'indicateurs';

    protected static ?string $recordTitleAttribute = 'label';

    protected static bool $shouldRegisterNavigation = true;

    /**
     * @param bool $isStandalone : Si true, affiche le sélecteur de Critère
     * @param int|null $parentCriteriaId : ID du critère parent (si connu, cas imbriqué)
     */
    public static function getFormSchema(bool $isStandalone = false, ?int $parentCriteriaId = null): array
    {
        return [
            Select::make('quality_label_filter')
                ->label('Label Qualité')
                ->options(QualityLabel::all()->pluck('label', 'id'))
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('criteria_id', null))
                ->visible($isStandalone)
                ->dehydrated(false)
                ->columnSpanFull(),

            Select::make('criteria_id')
                ->label('Critère parent')
                ->options(function (Get $get) {
                    $labelId = $get('quality_label_filter');

                    if (! $labelId) {
                        return Criteria::all()->pluck('label', 'id');
                    }

                    return Criteria::where('quality_label_id', $labelId)
                        ->pluck('label', 'id');
                })
                ->searchable()
                ->required()
                ->visible($isStandalone)
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state) {
                     if ($state) {
                        $criteria = Criteria::find($state);

                        $lastNumber = Indicator::where('criteria_id', $state)->max('number');
                        $next = $lastNumber ? intval($lastNumber) + 1 : 1;
                        $formattedNumber = str_pad($next, 2, '0', STR_PAD_LEFT);

                        $set('number', $formattedNumber);

                        if ($criteria && $criteria->order) {
                            $set('name', $criteria->order . '-' . $formattedNumber);
                        }
                     }
                })
                ->columnSpanFull(),

            TextInput::make('number')
                ->label('N°')
                ->placeholder('01')
                ->required()
                ->maxLength(2)
                ->default(function (Get $get) use ($parentCriteriaId) {
                    $parentCriteriaId = $parentCriteriaId ?? $get('criteria_id');

                    if ($parentCriteriaId) {
                        $lastNumber = Indicator::where('criteria_id', $parentCriteriaId)->max('number');
                        $next = $lastNumber ? intval($lastNumber) + 1 : 1;

                        return str_pad($next, 2, '0', STR_PAD_LEFT);
                    }
                    return '01';
                })
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {

                    $criteriaOrder = null;
                    $criteriaId = $get('criteria_id');

                    if ($criteriaId) {
                         $criteriaOrder = Criteria::find($criteriaId)?->order;
                    }
                    else {
                        $criteriaOrder = $get('../../order');
                    }

                    if ($criteriaOrder && $state) {
                        $set('name', $criteriaOrder . '-' . $state);
                    }
                })
                ->columnSpan(1),

            TextInput::make('label')
                ->label('Intitulé')
                ->required()
                ->columnSpan(1),

            TextInput::make('name')
                ->label('Slug (Debug)')
                ->disabled()
                ->dehydrated()
                ->required(),

            Textarea::make('description')
                ->label('Description')
                ->rows(5)
                ->maxLength(1500)
                ->columnSpanFull(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(self::getFormSchema(isStandalone: true));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('label'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Voir')
                    ->modalHeading(fn (Indicator $record) => $record->label)
                    ->modalFooterActionsAlignment('right'),

                EditAction::make()
                    ->iconButton()
                    ->tooltip('Modifier'),

                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Supprimer'),

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
