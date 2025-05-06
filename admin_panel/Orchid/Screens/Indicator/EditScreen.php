<?php

namespace Admin\Orchid\Screens\Indicator;

use Models\Criteria;
use Illuminate\Http\Request;

use Models\Indicator;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;
use Admin\Orchid\Layouts\Indicator\EditLayout;


class EditScreen extends Screen
{
    /**
     * @var Indicator
     */
    public $indicator;

    /**
     * @var string
     */
    protected $cancelRouteName = 'platform.quality.quality_label.indicators';

    /**
     * @var string
     */
    protected $redirectRouteName = 'platform.quality.quality_label.indicators';

    /**
     * Query data.
     *
     * @param Indicator
     * @return array
     */
    public function query($qualityLabelId, Indicator $indicator): iterable
    {
        $indicator->qualityLabel()->associate(intval($qualityLabelId));
        $this->indicator = $indicator;
        // dd($this->indicator);
        return [
            'indicator' => $indicator,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->indicator->exists ? __('indicator_edit :label', ['label' => $this->indicator->label]) : __('indicator_create');
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        // return $this->indicator->exists ? __('indicator_description :desc', ['desc' => $this->indicator->description]) : __('New indicator');
        return $this->indicator->exists ? __('indicator_description :desc', ['desc' => "Indicateur à modifier"]) : __('New indicator');
    }

    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.quality.quality_label.indicator.edit',
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Indicator[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Cancel'))
                ->icon('action-undo')
                ->route($this->cancelRouteName, ['quality_label' => $this->indicator->qualityLabel]),

            Button::make('Save', __('Save'))
                ->icon('check')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('indicator_remove_confirmation'))
                ->method('remove', [
                    'indicator' => $this->indicator->id,
                ])
                ->canSee($this->indicator->exists),

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
            EditLayout::class
        ];
    }

    /**
     * @param Indicator    $indicator
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
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
        $criteria = Criteria::find($indicatorData['criteria_id']);
        $indicatorData["name"] = $criteria->order . "-" . $indicatorData['number'];

        //Create Indicator model
        $indicator->fill($indicatorData)
            ->qualityLabel()
            ->associate(intval($indicatorData['quality_label_id']))
            ->criteria()
            ->associate(intval($indicatorData['criteria_id']))
            ->save();

        Toast::success(__('Indicator_was_saved'));
        return redirect()->route($this->redirectRouteName, ["quality_label" => $indicatorData['quality_label_id']]);
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function remove(Request $request)
    {
        $indicator = Indicator::findOrFail($request->get('indicator'));

        $qualityLabel = $indicator->qualityLabel;

        $indicator->delete();

        Toast::success(__('Indicator_was_removed'));
        return redirect()->route($this->redirectRouteName, ["quality_label" => $qualityLabel]);
    }
}
