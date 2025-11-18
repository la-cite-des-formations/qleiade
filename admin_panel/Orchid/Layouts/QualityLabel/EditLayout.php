<?php

namespace Admin\Orchid\Layouts\QualityLabel;

use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload; // RECONSTRUCTION : Remplacement de Cropper

class EditLayout extends Rows
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'qualityLabel';

    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        // RECONSTRUCTION : Correction du bug 'exists on null'
        // On vérifie si $qualityLabel n'est pas null *avant* de tester 'exists'
        $isEdit = $this->query->get('qualityLabel')?->exists ?? false;


        return [
            Input::make('qualityLabel.id')
                ->hidden(),

            Input::make('qualityLabel.label')
                ->title(__('quality_label_label'))
                ->required(),

            Input::make('qualityLabel.criterias_count_expected')
                ->title(__('criterias_count_expected'))
                ->type('number')
                ->min(1)
                ->max(20)
                ->required()
                ->disabled($isEdit),

            Input::make('qualityLabel.indicator_count_expected')
                ->title(__("indicators_count_expected"))
                ->type('number')
                ->min(1)
                ->max(200)
                ->required(),

            // RECONSTRUCTION : Remplacement de Cropper par Upload (v13+)
            Upload::make('qualityLabel.image')
                ->title(__('Image'))
                ->acceptedFiles('image/*') // Accepte tous les types d'images
                ->maxFiles(1)               // Limite à un seul fichier
                ->disk('images'),

            // RECONSTRUCTION : Remplacement de Input par Quill
            Quill::make('qualityLabel.description')
                ->title('Description')
                ->popover("Soyez concis s'il vous plait"),
        ];
    }
}
