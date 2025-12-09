<?php

namespace App\Filament\Admin\Resources\Criterias;

use App\Filament\Admin\Resources\Criterias\Pages\ManageCriterias;
use Models\Criteria;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CriteriaResource extends Resource
{
    protected static ?string $model = Criteria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;
    protected static ?string $navigationParentItem = 'Labels Qualité';
    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Critères';

    protected static ?string $modelLabel = 'Critère';

    protected static ?string $pluralModelLabel = 'Critères';

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quality_label_id')
                    ->relationship('qualityLabel', 'label')
                    ->label('Label Qualité')
                    ->preload(),
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
                TextInput::make('order')
                    ->numeric()
                    ->label('Ordre'),
                Textarea::make('description')
                    ->maxLength(500)
                    ->label('Description')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->label('Critère')
                    ->verticalAlignment('start'),
                TextColumn::make('qualityLabel.label')
                    ->sortable()
                    ->label('Label Qualité')
                    ->verticalAlignment('start'),
                TextColumn::make('description')
                    ->searchable()
                    ->label('Description')
                    ->wrap(),
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
            'index' => ManageCriterias::route('/'),
        ];
    }
}
