<?php

namespace App\Filament\Resources\Actions;

use App\Filament\Resources\Actions\Pages\ManageActions;
use Models\Action;
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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Models\Stage;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'activité';
    protected static ?string $pluralModelLabel = 'activités';

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Intitulé')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('name', $state);
                    }),

                Hidden::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(5)
                    ->columnSpanFull()
                    ->maxLength(1500),

                Select::make('stage_id')
                    ->label('Étape (vis à vis de la formation)')
                    ->options(function () {
                        static $options = NULL;

                        if (is_null($options)) {
                            $options = Stage::all()
                            ->sortBy(function ($stage) {
                                $position = array_search($stage->label, [
                                    1 => 'Avant',
                                    2 => 'Pendant',
                                    3 => 'Après',
                                ]);

                                return $position ?: 999;
                            })
                            ->pluck('label', 'id');
                        }

                        return $options;
                    })
                    ->required()
                    ->searchable(),

                TextInput::make('order')
                    ->label('Ordre')
                    ->integer()
                    ->minValue(1)
                    ->nullable(),

            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description')
                    ->label('Description')
                    ->prose()
                    ->placeholder('Aucune description renseignée.')
                    ->columnSpanFull(),

                TextEntry::make('stage.label')
                    ->label('Étape (vis à vis de la formation)')
                    ->badge()
                    ->color('info'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->label('Activité')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Voir')
                    ->modalHeading(fn (Action $record) => "{$record->order} - {$record->label}")
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
            'index' => ManageActions::route('/'),
        ];
    }
}
