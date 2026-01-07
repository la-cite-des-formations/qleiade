<?php

namespace App\Filament\Admin\Resources\Actions;

use App\Filament\Admin\Resources\Actions\Pages\ManageActions;
use Models\Action;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquare3Stack3d;
    protected static ?int $navigationSort = 50;

    protected static ?string $navigationLabel = 'Activités';

    protected static ?string $modelLabel = 'Activité';

    protected static ?string $pluralModelLabel = 'Activités';

    protected static ?string $recordTitleAttribute = 'label';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description')
                    ->hiddenLabel()
                    ->placeholder("Description de l'activité non renseignée")
                    ->columnSpanFull(),
                TextEntry::make('order')
                    ->label('Ordre')
                    ->inlineLabel(),
                TextEntry::make('stage.description')
                    ->hiddenLabel()
                    ->columnSpanFull(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom')
                    ->columnStart(1),
                Textarea::make('description')
                    ->maxLength(1500)
                    ->label('Description')
                    ->rows(5)
                    ->columnSpanFull(),
                Select::make('stage_id')
                    ->relationship('stage', 'label')
                    ->label('Étape')
                    ->columnStart(1),
                TextInput::make('order')
                    ->label('Ordre')
                    ->numeric()
                    ->minValue(1)
                    ->columnStart(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('order')
                    ->sortable()
                    ->label('Ordre')
                    ->verticalAlignment('start'),
                TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->label('Nom')
                    ->wrap()
                    ->verticalAlignment('start'),
                TextColumn::make('stage.label')
                    ->searchable()
                    ->sortable()
                    ->label('Étape')
                    ->verticalAlignment('start'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon(Heroicon::Eye)
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip(__('filament-actions::view.single.label'))
                    ->modalWidth('xl')
                    ->modalHeading(fn(Action $action): string => "{$action->label}"),
                EditAction::make()
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip(__('filament-actions::edit.single.label'))
                    ->modalWidth('2xl'),
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
            'index' => ManageActions::route('/'),
        ];
    }
}
