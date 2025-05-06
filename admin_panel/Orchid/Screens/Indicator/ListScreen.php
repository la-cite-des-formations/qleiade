<?php

namespace Admin\Orchid\Screens\Indicator;

use Admin\Orchid\Layouts\Indicator\ListLayout;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Models\QualityLabel;

class ListScreen extends Screen
{

    /**
     * @var QualityLabel
     */
    public $qualityLabel;

    /**
     * @return bool
     */
    protected function striped(): bool
    {
        return true;
    }

    /**
     * Query data.
     *
     * @return array
     */
    public function query(QualityLabel $qualityLabel): iterable
    {
        $this->qualityLabel = $qualityLabel;
        return ['qualityLabel.indicators' => $qualityLabel->indicators];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->qualityLabel->exists ? __('indicators for :label quality label', ['label' => $this->qualityLabel->label]) : __('no');
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Return to quality label list'))
                ->icon('action-undo')
                ->route('platform.quality.quality_labels'),

            Link::make(__('Save and go to :label', ["label" => $this->qualityLabel->label]))
                ->icon('save')
                ->route('platform.quality.quality_label.edit', $this->qualityLabel),

            Link::make(__('Add'))
                ->icon('plus')
                ->route('platform.quality.quality_label.indicator.create', ['quality_label' => $this->qualityLabel])
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            ListLayout::class,
        ];
    }
}
