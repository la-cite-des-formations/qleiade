<?php

namespace App\Filament\Resources\QualityLabels;

use App\Filament\Resources\Criterias\CriteriaResource;
use App\Filament\Resources\Indicators\IndicatorResource;
use App\Filament\Resources\QualityLabels\Pages\ManageQualityLabels;
use Models\QualityLabel;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class QualityLabelResource extends Resource
{
    protected static ?string $model = QualityLabel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $modelLabel = 'label qualité';
    protected static ?string $pluralModelLabel = 'labels qualité';

    protected static ?string $recordTitleAttribute = 'label';

    /**
     * Retourne le schéma du fomulaire d'identité d'un label qualité
     */
    public static function getIdentityFormSchema(): array
    {
        return [
            TextInput::make('label')
                ->label('Intitulé')
                ->required()
                ->maxLength(191)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('name', Str::slug($state))),

            Hidden::make('name')
                ->required()
                ->unique(ignoreRecord: true),

            Textarea::make('description')
                ->label('Description')
                ->rows(5)
                ->maxLength(1500)
                ->columnSpanFull(),

            FileUpload::make('image')
                ->label('Logo')
                ->directory('quality-labels')
                ->image()
                ->imageEditor()
                ->columnSpanFull(),
        ];
    }

    /**
     * Retourne le schéma de l'onglet "Critères" pour un label qualité donné
     * @param int|null $qualityLabelId : L'ID du label qualité associé
     */
    public static function getCriteriaTabSchema(?int $qualityLabelId): array
    {
        return [
            Repeater::make('criterias')
                ->label('Liste des Critères')
                ->relationship()
                ->collapsible()
                ->collapsed()
                ->itemLabel(fn (array $state): ?string =>
                    ($state['order'] ?? '?') . '. ' . ($state['label'] ?? 'Nouveau critère')
                )
                ->reorderable(false)
                ->orderColumn('order')
                ->defaultItems(0)
                ->columns(2)
                ->schema([
                    ...CriteriaResource::getFormSchema(
                        qualityLabelId: $qualityLabelId,
                        isStandalone: false
                    ),
                ])
                ->addActionLabel('+ Ajouter un critère'),
        ];
    }

    /**
     * Retourne le schéma de l'onglet "Indicateurs" pour un label qualité donné
     */
    public static function getIndicatorTabSchema(): array
    {
        return [
            Repeater::make('indicators')
                ->label('Liste des Indicateurs')
                // ->relationship('indicators')
                ->collapsible()
                ->collapsed()
                ->itemLabel(fn (array $state): ?string =>
                    ($state['number'] ?? '??') . ' - ' . ($state['label'] ?? 'Nouvel indicateur')
                )
                ->defaultItems(0)
                ->columnSpanFull()
                ->columns(6)
                ->schema([
                    ...IndicatorResource::getFormSchema(
                        isStandalone: true,
                        parentCriteriaId: null
                    )
                ])
                ->addActionLabel('+ Ajouter un indicateur')
                ->reorderable(false),
        ];
    }

    /**
     * Retourne le schéma complet de la modale de création/modifiation d'un label qualité
     * @param bool $isStandalone : Si true, retourne uniquement les champs d'identité (sans les onglets ni la structure)
     * @param int|null $qualityLabelId : L'ID du label qualité associé
     */
    public static function getModalSchema(bool $isStandalone = false, ?int $qualityLabelId = null): array
    {
        if ($isStandalone) {
            return self::getIdentityFormSchema();
        }

        return [
            Tabs::make('Referentiel')
                ->tabs([
                    Tabs\Tab::make('Identité')
                        ->icon(Heroicon::OutlinedInformationCircle)
                        ->schema(self::getIdentityFormSchema()),

                    Tabs\Tab::make('Critères')
                        ->icon(Heroicon::OutlinedListBullet)
                        ->hidden(fn ($record) => $record === null)
                        ->schema(self::getCriteriaTabSchema($qualityLabelId)),

                    Tabs\Tab::make('Indicateurs')
                        ->icon(Heroicon::OutlinedArrowUpCircle)
                        ->hidden(fn ($record) => $record === null || $record->criterias()->count() === 0)
                        ->schema(self::getIndicatorTabSchema()),
                ])
                ->columnSpanFull(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(self::getModalSchema(
                isStandalone: false,
                qualityLabelId: $schema->getRecord()?->id
            ));
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
                    ->modalHeading(fn (QualityLabel $record) => $record->label)
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
            'index' => ManageQualityLabels::route('/'),
        ];
    }
}
