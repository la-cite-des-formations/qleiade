<?php

namespace App\Filament\Admin\Resources\QualityLabels;

use App\Filament\Admin\Resources\QualityLabels\Pages\ManageQualityLabels;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Models\QualityLabel;
use Models\Criteria;
use Models\Indicator;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class QualityLabelResource extends Resource
{
    protected static ?string $model = QualityLabel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Labels Qualité';
    protected static ?string $modelLabel = 'Label Qualité';
    protected static ?string $pluralModelLabel = 'Labels Qualité';

    protected static ?string $recordTitleAttribute = 'label';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description')
                    ->hiddenLabel(),
                RepeatableEntry::make('criterias')
                    ->label(
                        fn(QualityLabel $qualityLabel): string =>
                        "{$qualityLabel->indicators_count} indicateurs répartis sur {$qualityLabel->criterias_count} critères :"
                    )
                    ->schema([
                        TextEntry::make('indicators')
                            ->label(fn(Criteria $criteria): string => "{$criteria->label} - {$criteria->description} :")
                            ->state(
                                fn(Criteria $criteria): string => $criteria->indicators
                                    ->sortBy('number', SORT_NATURAL)
                                    ->map(fn(Indicator $indicator) => "{$indicator->number} - {$indicator->label}")
                                    ->join('<br>')
                            )
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->extraAttributes(['class' => 'bg-info-list'])
                    ->columnSpanFull(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('name', Str::slug($state))),
                TextInput::make('name')
                    ->maxLength(255)
                    ->label('Identifiant')
                    ->hidden()
                    ->dehydrated()
                    ->required(),
                Textarea::make('description')
                    ->maxLength(1500)
                    ->label('Description')
                    ->rows(5)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Logo')
                    ->image()
                    ->directory('quality-labels')
                    ->columnSpanFull(),
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
                    ->label('Label')
                    ->verticalAlignment('start'),
                TextColumn::make('description')
                    ->searchable()
                    ->label('Description')
                    ->wrap(),
                TextColumn::make('criterias_count')
                    ->label('Critères')
                    ->alignRight()
                    ->sortable()
                    ->verticalAlignment('start'),
                TextColumn::make('indicators_count')
                    ->label('Indicateurs')
                    ->alignRight()
                    ->sortable()
                    ->verticalAlignment('start'),
                TextColumn::make('last_audit_date')
                    ->date('d/m/Y')
                    ->label('Dernier audit')
                    ->verticalAlignment('start'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon(Heroicon::Eye)
                    ->iconButton()
                    ->hiddenLabel()
                    ->tooltip(__('filament-actions::view.single.label'))
                    ->modalHeading(fn(QualityLabel $qualityLabel): string => "{$qualityLabel->label}"),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['criterias', 'indicators']);
    }
}
