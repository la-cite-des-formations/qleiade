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
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquare3Stack3d;
    protected static ?int $navigationSort = 60;

    protected static ?string $navigationLabel = 'Activités';

    protected static ?string $modelLabel = 'Activité';

    protected static ?string $pluralModelLabel = 'Activités';

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('name', \Illuminate\Support\Str::slug($state))),
                TextInput::make('name')
                    ->maxLength(255)
                    ->label('Identifiant')
                    ->hidden()
                    ->dehydrated()
                    ->required(),
                TextInput::make('description')
                    ->maxLength(255)
                    ->label('Description'),
                TextInput::make('order')
                    ->numeric()
                    ->label('Ordre'),
                Select::make('stage_id')
                    ->relationship('stage', 'label')
                    ->label('Étape')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('order')
                    ->sortable()
                    ->label('Ordre'),
                TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->label('Nom'),
                TextColumn::make('stage.label')
                    ->sortable()
                    ->label('Étape'),
                TextColumn::make('description')
                    ->searchable()
                    ->label('Description')
                    ->limit(40),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon(Heroicon::Eye)
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip(__('filament-actions::view.single.label')),
                EditAction::make()
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip(__('filament-actions::edit.single.label')),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageActions::route('/'),
        ];
    }
}
