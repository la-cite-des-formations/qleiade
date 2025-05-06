<?php

namespace Admin\Orchid\Layouts\QualityLabel;

use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Cropper;

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
                ->disabled(!is_null($this->query->get('criteria'))),

            Input::make('qualityLabel.indicator_count_expected')
                ->title(__("indicators_count_expected"))
                ->type('number')
                ->min(1)
                ->max(200)
                ->required(),
            //OBSERVE imposer la dimension, voir à l'usage
            Cropper::make('qualityLabel.image')
                ->storage('images')
                ->disk('images')
                ->group('quality_label')
                ->targetId()
                ->acceptedFiles('.png') // Accepted file types (images)
                ->title(__('Image')),

            Quill::make('qualityLabel.description')
                ->title('Description')
                ->popover("Soyez concis s'il vous plait"),
        ];
    }
}
