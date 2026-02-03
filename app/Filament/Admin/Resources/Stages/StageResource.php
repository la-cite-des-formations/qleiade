<?php

namespace App\Filament\Admin\Resources\Stages;

use App\Filament\Admin\Resources\Stages\Pages\ManageStages;
use Models\Stage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    protected static ?string $navigationParentItem = 'Activités';
    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Étapes';

    protected static ?string $modelLabel = 'Étape';

    protected static ?string $pluralModelLabel = 'Étapes';

    protected static ?string $recordTitleAttribute = 'label';


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
            ]);
    }

    private static function getTableColumns(): array
    {
        return [
            TextColumn::make('label')
                ->searchable()
                ->sortable()
                ->label('Nom')
                ->wrap()
                ->verticalAlignment('start'),
            TextColumn::make('description')
                ->searchable()
                ->label('Description')
                ->wrap()
                ->verticalAlignment('start'),
            TextColumn::make('actions_count')
                ->counts('actions')
                ->label('Activités')
                ->alignRight()
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
            'index' => ManageStages::route('/'),
        ];
    }
}
