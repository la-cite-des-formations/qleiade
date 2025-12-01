<?php

namespace App\Filament\Resources\Criterias;

use App\Filament\Resources\Criterias\Pages\ManageCriterias;
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
use Illuminate\Support\Str;

class CriteriaResource extends Resource
{
    protected static ?string $model = Criteria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    protected static ?string $modelLabel = 'critère';
    protected static ?string $pluralModelLabel = 'critères';

    protected static ?string $recordTitleAttribute = 'label';

    protected static bool $shouldRegisterNavigation = true;

    public static function getFormSchema(?int $qualityLabelId = null, bool $isStandalone = false): array
    {
        return [
            Select::make('quality_label_id')
                ->label('Label Qualité')
                ->relationship('qualityLabel', 'label')
                ->searchable()
                ->preload()
                ->required()
                ->visible($isStandalone) // 👀 Visible seulement si autonome
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state) {
                     if ($state) {
                         $maxOrder = Criteria::where('quality_label_id', $state)->max('order');
                         $set('order', $maxOrder ? $maxOrder + 1 : 1);
                     }
                })
                ->columnSpanFull(),

            TextInput::make('order')
                ->label('Ordre')
                ->integer()
                ->default(function (Get $get) use ($qualityLabelId) {
                    $qualityLabelId = $qualityLabelId ?? $get('quality_label_id');

                    if ($qualityLabelId) {
                        return Criteria::where('quality_label_id', $qualityLabelId)->max('order') + 1;
                    }

                    return 1;
                })
                ->minValue(1)
                ->required()
                ->columnSpan(1),

            TextInput::make('label')
                ->label('Intitulé')
                ->required()
                ->maxLength(191)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('name', Str::slug($state)))
                ->columnSpan(4),

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
                    ->modalHeading(fn (Criteria $record) => $record->label)
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
            'index' => ManageCriterias::route('/'),
        ];
    }
}
