<?php

namespace Admin\Orchid\Screens\QualityLabel;

use Models\Criteria;
use Models\QualityLabel;
use Admin\Orchid\Layouts\QualityLabel\EditLayout;
use Admin\Orchid\Layouts\QualityLabel\ListLayout;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Str; // Assurez-vous que Str est importé
use Illuminate\Support\Arr; // Assurez-vous que Arr est importé


class ListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        // RECONSTRUCTION : Utilisation de la pagination
        return [
            "quality_labels" => QualityLabel::paginate()
        ];
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
                ->method('createOrUpdateQualityLabel') // Nom de méthode unifié
                ->icon('plus')
                ->modalTitle(__('quality_label_create')), // Titre pour la création
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
            ])
            ->title(__('quality_label_create')) // Titre par défaut (sera écrasé par le bouton Editer)
            ->async('asyncGetQualityLabel'), // Méthode pour charger les données en mode édition

            ListLayout::class,
        ];
    }

    /**
     * Méthode AJAX pour charger un label dans le modal
     */
    public function asyncGetQualityLabel(Request $request): iterable
    {
        $qualityLabel = QualityLabel::findOrNew($request->input('qualityLabel'));

        return [
            'qualityLabel' => $qualityLabel,
        ];
    }


    /**
     * Méthode de sauvegarde unifiée (Création ET Modification)
     */
    public function createOrUpdateQualityLabel(Request $request, QualityLabel $qualityLabel) // Orchid injecte un modèle vide si $request->input('qualityLabel.id') n'existe pas
    {
        $request->validate([
            'qualityLabel.label' => "required",
        ]);

        // On récupère l'ID avant de 'filler' (pour savoir si c'est une création)
        $qualityLabelId = $request->input('qualityLabel.id');

        // 1. On récupère toutes les données du formulaire
        $data = $request->input('qualityLabel');

        // 2. On utilise 'Arr::except' pour retirer le champ 'image'
        $dataToFill = Arr::except($data, ['image']);

        // 3. LA CORRECTION : On ajoute la logique métier manquante
        // (On génère le 'name' à partir du 'label')
        if (isset($dataToFill['label'])) {
            $dataToFill['name'] = Str::slug($dataToFill['label']);
        }

        // 4. On 'fill' le modèle *uniquement* avec les données "sûres"
        $qualityLabel->fill($dataToFill);

        // 5. On sauvegarde.
        $qualityLabel->save();

        Toast::info(__('quality_label_was_saved'));

        // Si c'est une *création* (pas d'ID), on exécute la logique "Criteria"
        if (is_null($qualityLabelId)) {

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
    }


    /**
     * Méthode de suppression
     */
    public function remove(Request $request): void
    {
        $qualityLabel = QualityLabel::findOrFail($request->get('qualityLabel'));
        $qualityLabel->delete();
        Toast::info(__('quality_label_was_removed'));
    }
}
