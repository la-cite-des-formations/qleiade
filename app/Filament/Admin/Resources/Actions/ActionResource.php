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
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('stage_id')
                    ->relationship('stage', 'label')
                    ->label('Étape')
                    ->placeholder('Choisir...')
                    ->native(false)
                    ->columnStart(1),
                TextInput::make('order')
                    ->label('Ordre')
                    ->numeric()
                    ->minValue(1)
                    ->columnStart(1),
            ]);
    }

    private static function getTableColumns(): array
    {
        return [
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
        ];
    }

    private static function getTableFilters(): array
    {
        return [
            //
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
                ->modalHeading(fn(Action $action): string => "{$action->label}"),
            EditAction::make()
                ->icon(Heroicon::OutlinedPencilSquare)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::edit.single.label'))
                ->slideOver(),
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
            ->filters(self::getTableFilters())
            ->deferFilters(false)
            ->recordActions(self::getTableActions())
            ->toolbarActions(self::getTableBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageActions::route('/'),
        ];
    }
}
