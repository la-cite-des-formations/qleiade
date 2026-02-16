<?php

namespace App\Filament\Admin\Resources\Units;

use App\Filament\Admin\Resources\Units\Pages\ManageUnits;
use Models\Unit;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use App\Filament\Components\Tab;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static string|UnitEnum|null $navigationGroup = 'GESTION';
    protected static ?int $navigationSort = 50;

    protected static ?string $navigationLabel = 'Services';

    protected static ?string $modelLabel = 'Service';

    protected static ?string $pluralModelLabel = 'Services';

    protected static ?string $recordTitleAttribute = 'label';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('manager_name')
                    ->label('Responsable')
                    ->visible(fn($record) => $record->manager_name)
                    ->columnSpan(2),
                TextEntry::make('description')
                    ->label('Description')
                    ->visible(fn($record) => $record->description)
                    ->columnSpanFull(),
                RepeatableEntry::make('users')
                    ->hiddenLabel()
                    ->table([
                        TableColumn::make('Utilisateurs affectés'),
                    ])
                    ->schema([
                        TextEntry::make('name'),
                    ])
                    ->visible(fn($record) => $record->users->isNotEmpty())
                    ->columnSpanFull(),
            ]);
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->extraAttributes(['class' => 'flat-tabs'])
                    ->tabs([
                        Tab::make('Identité')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Identifiant')
                                    ->required()
                                    ->placeholder('Identifiant unique court')
                                    ->maxLength(191),
                                TextInput::make('label')
                                    ->required()
                                    ->maxLength(191)
                                    ->label('Nom')
                                    ->placeholder('Nom complet')
                                    ->columnSpan(2)
                                    ->columnStart(1),
                                TextInput::make('manager_name')
                                    ->maxLength(191)
                                    ->label('Responsable')
                                    ->placeholder('Nom du responsable')
                                    ->columnSpan(2),
                                Textarea::make('description')
                                    ->maxLength(1500)
                                    ->label('Description')
                                    ->placeholder('Description détaillée')
                                    ->columnSpanFull()
                                    ->rows(3),
                            ])
                            ->columns(4),
                        Tab::make('Utilisateurs')
                            ->schema([
                                Select::make('users')
                                    ->relationship('users', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->label('Utilisateurs affectés'),
                            ]),
                    ])
                    ->columnSpanFull(),
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
        ];
    }

    private static function getTableFilters(): array
    {
        return [
            SelectFilter::make('users')
                ->relationship('users', 'name')
                ->label('Utilisateurs affectés')
                ->multiple(),
        ];
    }

    private static function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::OutlinedEye)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::view.single.label'))
                ->modalHeading(fn($record) => "{$record->name} - {$record->label}")
                ->slideOver(),
            EditAction::make()
                ->icon(Heroicon::OutlinedPencilSquare)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::edit.single.label'))
                ->extraModalWindowAttributes(['class' => 'modal-no-padding'])
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
