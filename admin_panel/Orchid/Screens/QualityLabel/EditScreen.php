<?php

namespace Admin\Orchid\Screens\QualityLabel;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     *
     * @param QualityLabel
     * @return array
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
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->qualityLabel->exists ? __('quality_label_edit :label', ['label' => $this->qualityLabel->label]) : __('quality_label_create');
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('quality_label_description');
    }

    //DOC: orchid add permission to a screen
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
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Cancel'))
                ->icon('action-undo')
                ->route('platform.quality.quality_labels'),

            Button::make('Save', __('Save'))
                ->icon('check')
                ->method('save'),

            // Button::make(__('Remove'))
            //     ->icon('trash')
            //     ->confirm(__('quality_label_remove_confirmation'))
            //     ->method('remove', [
            //         'quality_label' => $this->qualityLabel,
            //     ])
            //     ->canSee($this->qualityLabel->exists),
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
            Layout::modal('indicatorModal', IndicatorEditLayout::class)
                ->title(__('indicator_edit :label', ["label" => ""]))
                ->async('asyncGetIndicator'),

            Layout::modal('criteriaModal', CriteriaEditLayout::class)
                ->title(__('criteria_edit'))
                ->async('asyncGetCriteria'),


            //paramétrage des dashboard (et de la page de résultat?)
            //possibiliter de créer plusieurs dashboard pour un label
            //prévoir le routage en conséquence ex: qualiopi/dashboard/{?unit}
            // a voir mettre le procéssus dans user ???? ou ypareo pour choper les status des users
            //affichage par indicateurs
            //par unit
            //seulement les preuves
            //faire des graphs

            //paramétrages des kpi
            //types de kpi
            //les données liées
            Layout::tabs(
                [
                    __('quality_label') => QualityLabelLayout::class,
                    __('criterias') => CriteriasLayout::class,
                    __('indicators') => IndicatorsLayout::class,
                    // __('kpi') => Layout::view('platform::dummy.block'),
                    // __('dashboard') => Layout::view('platform::dummy.block'),
                ]
            )
        ];
    }

    /**
     * @param QualityLabel    $qualityLabel
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(QualityLabel $qualityLabel, Request $request)
    {

        $request->validate([
            // 'qualityLabel.label' => "required|regex:/^[a-zA-Z0-9\s]+$/"
            'qualityLabel.label' => "required",
            'qualityLabel.image.*' => 'mimes:jpeg,png,jpg,gif', // Validation spécifique à l'image
        ]);

        //Datas from request
        $qualityLabelData = $request->all('qualityLabel')['qualityLabel'];

        // format name code
        $qualityLabelData["name"] = Str::slug($qualityLabelData["label"]);

        //Create QualityLabel model
        $qualityLabel->fill($qualityLabelData)
            ->save();

        $qualityLabel->attachment()->syncWithoutDetaching(
            $request->input('qualityLabel.image', "")
        );


        Toast::success(__('quality_label_was_saved'));

        return redirect()->route('platform.quality.quality_labels');
    }

    /**
     * @param Indicator    $indicator
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
            ->qualityLabel()
            ->associate(intval($indicatorData['quality_label_id']))
            ->criteria()
            ->associate(intval($indicatorData['criteria_id']))
            ->save();

        Toast::success(__('Indicator_was_saved'));
    }

    /**
     * @param Criteria    $criteria
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
