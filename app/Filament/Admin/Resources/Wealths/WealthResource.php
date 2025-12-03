<?php

namespace App\Filament\Admin\Resources\Wealths;

use App\Filament\Admin\Resources\Wealths\Pages\ManageWealths;
use Models\Wealth;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WealthResource extends Resource
{
    protected static ?string $model = Wealth::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?int $navigationSort = 40;

    protected static ?string $navigationLabel = 'Preuves';

    protected static ?string $modelLabel = 'Preuve';

    protected static ?string $pluralModelLabel = 'Preuves';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom de la preuve'),
                Textarea::make('description')
                    ->maxLength(500)
                    ->label('Description')
                    ->rows(3),
                Select::make('unit_id')
                    ->relationship('unit', 'label')
                    ->required()
                    ->label('Service')
                    ->preload(),
                Select::make('wealth_type_id')
                    ->relationship('wealthType', 'label')
                    ->label('Type de preuve')
                    ->preload(),
                Select::make('indicators')
                    ->relationship('indicators', 'label')
                    ->multiple()
                    ->preload()
                    ->label('Indicateurs'),
                Select::make('actions')
                    ->relationship('actions', 'label')
                    ->multiple()
                    ->preload()
                    ->label('Activités'),
                Select::make('tags')
                    ->relationship('tags', 'label')
                    ->multiple()
                    ->preload()
                    ->label('Libellés'),
                TextInput::make('conformity_level')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(100)
                    ->label('Niveau de conformité (0-100)'),
                DateTimePicker::make('validity_date')
                    ->label('Date de validité'),
                Textarea::make('tracking')
                    ->maxLength(500)
                    ->label('Suivi')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nom'),
                TextColumn::make('unit.label')
                    ->sortable()
                    ->label('Service'),
                TextColumn::make('wealthType.label')
                    ->label('Type'),
                TextColumn::make('indicators.label')
                    ->badge()
                    ->label('Indicateurs')
                    ->limit(3),
                TextColumn::make('conformity_level')
                    ->sortable()
                    ->label('Conformité'),
                TextColumn::make('validity_date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->label('Validité'),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Créée le')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ManageWealths::route('/'),
        ];
    }
}
