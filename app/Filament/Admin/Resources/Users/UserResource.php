<?php

namespace App\Filament\Admin\Resources\Users;

use App\Filament\Admin\Resources\Users\Pages\ManageUsers;
use Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use App\Filament\Components\Tab;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static string|\UnitEnum|null $navigationGroup = 'GESTION';
    protected static ?int $navigationSort = 60;

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static ?string $modelLabel = 'Utilisateur';

    protected static ?string $pluralModelLabel = 'Utilisateurs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('email')
                    ->hiddenLabel(),
                RepeatableEntry::make('units')
                    ->hiddenLabel()
                    ->table([
                        TableColumn::make("Services affectés à l'utilisateur"),
                    ])
                    ->schema([
                        TextEntry::make('full'),
                    ])
                    ->visible(fn($record) => $record->units->isNotEmpty())
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
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(191)
                                    ->label('Email')
                                    ->unique(ignoreRecord: true)
                                    ->columnStart(1),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->autocomplete('new-password')
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->dehydrated(fn($state, $record) => filled($state) && ($record === null || !\Illuminate\Support\Facades\Hash::check($state, $record->password)))
                                    ->dehydrateStateUsing(fn($state) => \Illuminate\Support\Facades\Hash::make($state))
                                    ->label('Mot de passe')
                                    ->placeholder('••••••')
                                    ->maxLength(191),
                            ])
                            ->columns(2),
                        Tab::make('Services')
                            ->schema([
                                Select::make('units')
                                    ->relationship('units', 'label')
                                    ->multiple()
                                    ->preload()
                                    ->label('Services affectés'),
                            ]),
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
                ->label('Nom')
                ->verticalAlignment('start')
                ->width('25%'),
            TextColumn::make('units.name')
                ->label('Services')
                ->wrap()
                ->verticalAlignment('start'),
            TextColumn::make('permissions.name')
                ->badge()
                ->label('Permissions')
                ->limitList(2)
                ->verticalAlignment('start'),
        ];
    }

    private static function getTableFilters(): array
    {
        return [
            SelectFilter::make('units')
                ->relationship('units', 'label')
                ->label('Services affectés')
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
                ->modalHeading(fn($record) => $record->name)
                ->slideOver(),
            EditAction::make()
                ->icon(Heroicon::OutlinedPencilSquare)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::view.single.label'))
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
            ->filters(self::getTableFilters())
            ->deferFilters(false)
            ->recordActions(self::getTableActions())
            ->toolbarActions(self::getTableBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
