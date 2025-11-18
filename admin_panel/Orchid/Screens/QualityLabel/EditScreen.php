<?php

namespace Admin\Orchid\Screens\QualityLabel;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

use Models\QualityLabel;
use Admin\Orchid\Layouts\QualityLabel\EditLayout as QualityLabelLayout;
use Admin\Orchid\Layouts\Indicator\ListLayout as IndicatorsLayout;
use Admin\Orchid\Layouts\Indicator\EditLayout as IndicatorEditLayout;
use Admin\Orchid\Layouts\Criteria\ListLayout as CriteriasLayout;
use Admin\Orchid\Layouts\Criteria\EditLayout as CriteriaEditLayout;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

use Models\Indicator;
use Models\Criteria;

class EditScreen extends Screen
{
    /**
     * @var QualityLabel
     */
    public $qualityLabel;

    /**
     * @var string
     */
    public $activeTabName;

    /**
     * Query data.
     */
    public function query(QualityLabel $qualityLabel): iterable
    {
        $this->activeTabName = __('quality_label');
        $data = [
            'qualityLabel' => $qualityLabel
        ];
        return $data;
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return $this->qualityLabel->exists ? __('quality_label_edit :label', ['label' => $this->qualityLabel->label]) : __('quality_label_create');
    }

    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.quality.quality_label.edit',
        ];
    }

    /**
     * Button commands.
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Cancel'))
                ->icon('action-undo')
                ->route('platform.quality.quality_labels'),

            // Le nom "magique" 'save'
            Button::make(__('Save'))
                ->icon('check')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('quality_label_remove_confirmation'))
                ->method('remove', [
                    'qualityLabel' => $this->qualityLabel,
                ])
                ->canSee($this->qualityLabel->exists),
        ];
    }

    /**
     * Views.
     */
    public function layout(): iterable
    {
        return [
            Layout::modal('indicatorModal', IndicatorEditLayout::class)
                ->title(__('indicator_edit :label', ["label" => ""]))
                ->async('asyncGetIndicator'),

            Layout::modal('criteriaModal', CriteriaEditLayout::class)
                ->title(__('criteria_edit'))
                ->async('asyncGetCriteria'),

            Layout::tabs(
                [
                    __('quality_label') => QualityLabelLayout::class,
                    __('criterias') => CriteriasLayout::class,
                    __('indicators') => IndicatorsLayout::class,
                ]
            )
        ];
    }

    /**
     * Le nom "magique" 'save' avec la logique v13+ 'Arr::except'
     */
    public function save(QualityLabel $qualityLabel, Request $request)
    {
        $request->validate([
            'qualityLabel.label' => "required",
        ]);

        $qualityLabelData = $request->input('qualityLabel');

        $dataToFill = Arr::except($qualityLabelData, ['image']);
        $dataToFill["name"] = Str::slug($dataToFill["label"]);

        $qualityLabel->fill($dataToFill)
            ->save();

        Toast::success(__('quality_label_was_saved'));

        return redirect()->route('platform.quality.quality_labels');
    }

    // ... (Le reste des méthodes updateIndicator, updateCriteria, etc. sont ici) ...
    // ... (Elles sont inchangées et correctes) ...

    /**
     * @param Indicator     $indicator
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateIndicator(Request $request)
    {
        $request->validate([
            'indicator.label' => "required"
        ]);

        //Datas from request
        $indicatorData = $request->all('indicator')['indicator'];

        //si j'utilise la mécanique orchid problème d'id alors:
        $indicator = Indicator::find($indicatorData['id']);
        if (is_null($indicator)) {
            $indicator = new Indicator();
        }

        // format name code
        $indicatorData["name"] = Str::slug($indicatorData["label"]);

        //Create Indicator model
        $indicator->fill($indicatorData)
            ->criteria()
            ->associate(intval($indicatorData['criteria_id']))
            ->save();

        Toast::success(__('Indicator_was_saved'));
    }

    /**
     * @param Criteria     $criteria
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCriteria(Request $request)
    {
        $request->validate([
            'criteria.label' => "required"
        ]);

        //Datas from request
        $criteriaData = $request->all('criteria')['criteria'];

        //si j'utilise la mécanique orchid problème d'id alors:
        $criteria = Criteria::find($criteriaData['id']);
        if (is_null($criteria)) {
            $criteria = new Criteria();
        }

        // format name code
        $criteriaData["name"] = Str::slug($criteriaData["label"]);

        //Create Criteria model
        $criteria->fill($criteriaData)
            ->qualityLabel()
            ->associate(intval($criteriaData['quality_label_id']))
            ->save();

        Toast::success(__('Criteria_was_saved'));
    }

    public function asyncGetIndicator(Request $request)
    {
        $payload = $request->all();
        $data = [
            'indicator' => $payload['indicator'],
        ];
        return $data;
    }

    public function asyncGetCriteria(Request $request)
    {
        $payload = $request->all();
        $data = [
            'criteria' => $payload['criteria'],
        ];
        return $data;
    }


    /**
     * @param QualityLabel $qualityLabel
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function remove(QualityLabel $qualityLabel)
    {
        $qualityLabel->delete();
        Toast::success(__('quality_label_was_removed'));
        return redirect()->route('platform.quality.quality_labels');
    }
}
