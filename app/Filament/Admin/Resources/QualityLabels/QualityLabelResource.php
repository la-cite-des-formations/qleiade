<?php

namespace App\Filament\Admin\Resources\QualityLabels;

use App\Filament\Admin\Resources\QualityLabels\Pages\ManageQualityLabels;
use Models\QualityLabel;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QualityLabelResource extends Resource
{
    protected static ?string $model = QualityLabel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Labels Qualité';
    protected static ?string $modelLabel = 'Label Qualité';
    protected static ?string $pluralModelLabel = 'Labels Qualité';

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
                Textarea::make('description')
                    ->maxLength(500)
                    ->label('Description')
                    ->rows(3),
                TextInput::make('criterias_count_expected')
                    ->numeric()
                    ->label('Nombre de critères attendus'),
                TextInput::make('indicator_count_expected')
                    ->numeric()
                    ->label('Nombre d\'indicateurs attendus'),
                TextInput::make('audit_frequency')
                    ->numeric()
                    ->label('Fréquence d\'audit (mois)'),
                DatePicker::make('last_audit_date')
                    ->label('Date du dernier audit'),
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
                    ->label('Nom'),
                TextColumn::make('description')
                    ->searchable()
                    ->label('Description')
                    ->limit(50),
                TextColumn::make('criterias_count_expected')
                    ->label('Critères'),
                TextColumn::make('indicator_count_expected')
                    ->label('Indicateurs'),
                TextColumn::make('last_audit_date')
                    ->date('d/m/Y')
                    ->label('Dernier audit'),
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
            'index' => ManageQualityLabels::route('/'),
        ];
    }
}
