<?php

namespace App\Filament\Admin\Resources\Units;

use App\Filament\Admin\Resources\Units\Pages\ManageUnits;
use Models\Unit;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static ?int $navigationSort = 70;

    protected static ?string $navigationLabel = 'Services';

    protected static ?string $modelLabel = 'Service';

    protected static ?string $pluralModelLabel = 'Services';

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom du service'),
                TextInput::make('name')
                    ->maxLength(255)
                    ->label('Identifiant'),
                TextInput::make('manager_name')
                    ->maxLength(255)
                    ->label('Responsable'),
                TextInput::make('description')
                    ->maxLength(255)
                    ->label('Description'),
            ]);
    }

    private static function getTableColumns(): array
    {
        return [
            TextColumn::make('label')
                ->searchable()
                ->sortable()
                ->label('Service'),
            TextColumn::make('manager_name')
                ->searchable()
                ->label('Responsable'),
            TextColumn::make('description')
                ->searchable()
                ->label('Description')
                ->limit(40),
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
            'index' => ManageUnits::route('/'),
        ];
    }
}
