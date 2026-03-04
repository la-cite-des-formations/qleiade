<?php

namespace App\Filament\Admin\Resources\Tags;

use App\Filament\Admin\Resources\Tags\Pages\ManageTags;
use Models\Tag;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static string|\UnitEnum|null $navigationGroup = 'DONNÉES';
    protected static ?int $navigationSort = 40;

    protected static ?string $navigationLabel = 'Libellés';

    protected static ?string $modelLabel = 'Libellé';

    protected static ?string $pluralModelLabel = 'Libellés';

    protected static ?string $recordTitleAttribute = 'label';

    public static function getWidgetDescription(): string
    {
        return 'Catégoriser les éléments avec des libellés personnalisés.';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom'),
                RichEditor::make('description')
                    ->maxLength(1500)
                    ->label('Description')
                    ->extraInputAttributes(['style' => 'min-height: 20em;'])
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
                ->verticalAlignment('start')
                ->wrap()
                ->width('25%'),
            TextColumn::make('description')
                ->searchable()
                ->label('Description')
                ->html()
                ->verticalAlignment('start')
                ->wrap(),
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
                ->slideOver()
                ->icon(Heroicon::OutlinedPencilSquare)
                ->iconButton()
                ->hiddenLabel()
                ->tooltip(__('filament-actions::edit.single.label')),
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
            'index' => ManageTags::route('/'),
        ];
    }
}
