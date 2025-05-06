<?php

namespace Admin\Orchid\Layouts\QualityLabel;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Models\QualityLabel;

use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;

class ListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'quality_labels';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make(__('label'))
                ->sort()
                ->render(function (QualityLabel $qualityLabel) {
                    return $qualityLabel->label;
                }),

            TD::make(__('description'))
                ->sort()
                ->render(function (QualityLabel $qualityLabel) {
                    //NOTE : add function getFirstSentenceOfHtml($html) to a trait if anothers
                    $truncatedDesc = Str::before(Str::after($qualityLabel->description, '<p>'), '</p>') . ' ...';
                    return $truncatedDesc;
                }),

            TD::make(__('Actions_form'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (QualityLabel $qualityLabel) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.quality.quality_label.edit', $qualityLabel->id)
                                ->icon('pencil'),

                            Link::make(__('Edit its indciators'))
                                ->route('platform.quality.quality_label.indicators', ["quality_label" => $qualityLabel])
                                ->icon('equalizer'),

                            // Button::make(__('Delete'))
                            //     ->icon('trash')
                            //     ->confirm(__('qualityLabel_remove_confirmation'))
                            //     ->method('remove', [
                            //         'id' => $qualityLabel->id,
                            //     ]),
                        ])
                        ->canSee(Auth::user()->hasAccess('platform.quality.quality_label.edit'));
                }),
        ];
    }
}
