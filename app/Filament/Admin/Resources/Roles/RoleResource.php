<?php

namespace App\Filament\Admin\Resources\Roles;

use App\Filament\Admin\Resources\Roles\Pages\ManageRoles;
use Models\Role;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Resource;
use App\Filament\Components\Tab;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Components\Tabs;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static string|\UnitEnum|null $navigationGroup = 'GESTION';
    protected static ?int $navigationSort = 70;

    protected static ?string $navigationLabel = 'Rôles';

    protected static ?string $modelLabel = 'Rôle';

    protected static ?string $pluralModelLabel = 'Rôles';

    protected static ?string $recordTitleAttribute = 'name';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('guard_name')
                    ->label("Espace d'authentification"),
                RepeatableEntry::make('permissions')
                    ->hiddenLabel()
                    ->table([
                        TableColumn::make("Permissions associées"),
                    ])
                    ->schema([
                        TextEntry::make('name'),
                    ])
                    ->visible(fn($record) => $record->permissions->isNotEmpty())
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
                                    ->required()
                                    ->maxLength(191)
                                    ->label('Nom'),
                                TextInput::make('guard_name')
                                    ->required()
                                    ->maxLength(191)
                                    ->default('web')
                                    ->label("Espace d'authentification")
                                    ->columnStart(1),
                            ])
                            ->columns(2),
                        Tab::make('Permissions')
                            ->schema([
                                CheckboxList::make('permissions')
                                    ->relationship('permissions', 'name')
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->bulkToggleable()
                                    ->searchable()
                                    ->label('Droits d\'accès'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->label('Nom'),
            TextColumn::make('guard_name')
                ->label("Espace d'authentification"),
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
                ->modalHeading(fn($record) => $record->name)
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
            ->recordTitleAttribute('name')
            ->columns(self::getTableColumns())
            ->extraAttributes(['class' => 'resource-table'])
            ->recordActions(self::getTableActions())
            ->toolbarActions(self::getTableBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRoles::route('/'),
        ];
    }
}
