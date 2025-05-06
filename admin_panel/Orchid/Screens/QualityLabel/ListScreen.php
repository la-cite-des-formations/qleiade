<?php

namespace Admin\Orchid\Screens\QualityLabel;

use Models\Criteria;
use Models\QualityLabel;
use Admin\Orchid\Layouts\QualityLabel\EditLayout;
use Admin\Orchid\Layouts\QualityLabel\ListLayout;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;

class ListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return ["quality_labels" => QualityLabel::all()];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('quality_labels');
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('quality_label_create'))
                ->modal('qualityLabelModal')
                ->method('createQualityLabel')
                ->icon('plus'),
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
            Layout::modal('qualityLabelModal', [
                EditLayout::class
            ])->title(__('quality_label_create')),
            ListLayout::class,
        ];
    }

    /**
     * The action that will take place when
     * the form of the modal window is submitted
     */
    public function createQualityLabel(Request $request)
    {
        $request->validate([
            'qualityLabel.label' => "required|regex:/^[a-zA-Z0-9\s]+$/"
        ]);

        $data = $request->all('qualityLabel')['qualityLabel'];

        //création du label en bdd
        try {
            $qualityLabel = $this->save($data);
            $qualityLabel->attachment()->syncWithoutDetaching(
                $data['image']
            );
            Toast::info(__('quality_label_was_saved'));
        } catch (\Throwable $th) {
            Toast::error(__('quality_label_saved_error'));
        }

        //Create criterias according to request criteria number expected
        for ($i = 0; $i < $data['criterias_count_expected']; $i++) {
            $order = $i + 1;
            $criteria = new Criteria([
                'name' => "criteria_" . $order,
                'label' => "Critère " . $order,
                'order' => $order,
                'description' => 'CAGMJ: critère auto generé à mettre à jour',
            ]);
            $criteria
                ->qualityLabel()
                ->associate($qualityLabel->id)
                ->save();
        }

        Toast::info('Cliquer sur ajouter pour créer de nouveaux indicateurs');

        //rediriger sur liste indicateurs avec l'id du label créé et incité à créer le premier indicateur
        return redirect(route('platform.quality.quality_label.indicators', ["quality_label" => $qualityLabel]));
    }

    /**
     * @param QualityLabel    $qualityLabel
     * @param Request $request
     *
     * @return QualityLabel
     */
    public function save($data)
    {
        $qualityLabel = new QualityLabel();
        // format name code
        $data["name"] = Str::slug($data["label"]);

        try {
            //Create QualityLabel model
            $qualityLabel->fill($data)
                ->save();
            // Toast::success(__('quality_label_was_saved'));
        } catch (\Throwable $th) {
            // Toast::error(__('quality_label_saved_error'));
            throw $th;
        }

        return $qualityLabel;
    }

    //  /**
    //  * @param Request $request
    //  */
    // public function remove(Request $request): void
    // {
    //     $tag = QualityLabel::findOrFail($request->get('id'));

    //     $tag->delete();
    //     Toast::info(__('Quality_label_was_removed'));
    // }
}
