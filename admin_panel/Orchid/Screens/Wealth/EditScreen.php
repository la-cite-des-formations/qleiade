<?php

namespace Admin\Orchid\Screens\Wealth;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Facades\Layout;

use Models\Tag;
use Models\Action;
use Models\Wealth;
use Models\Indicator;
use Models\WealthType;
use Models\File as FileModel;
use Models\FileWealth;
use App\Http\Traits\DriveManagement;
use App\Http\Traits\WithAttachments;
use App\Http\Requests\WealthRequest;

use Admin\Orchid\Layouts\Wealth\EditLayout;
use Admin\Orchid\Layouts\Wealth\RelationsLayout;
use Admin\Orchid\Layouts\Wealth\AttachmentListener;
use Admin\Orchid\Layouts\Wealth\GranularityListener;
use Admin\Orchid\Layouts\Wealth\UnitListener;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Models\QualityLabel;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\TD;

class EditScreen extends Screen
{
    use DriveManagement, WithAttachments;

    /**
     * @var Wealth
     */
    public $wealth;

    /**
     * @var bool
     */
    public $duplicate;

    /**
     * Query data.
     *
     * @param Wealth
     * @return array
     */
    public function query(Wealth $wealth, $duplicate, Request $request): iterable
    {
        $datas = [];

        // 1) Récupère le whoShouldSee depuis la session, si présent
        $datas['whoShouldSee'] = $request->session()->get('whoShouldSee', '');

        // 2) Nouveau / Edit
        if (!$wealth->exists) {
            # create
            $datas['wealth'] = $wealth;
            $datas['duplicate'] = false;
        }
        else {
            // édition ou duplication
            if (is_null($duplicate)) {
                // édition
                $wealth->wealth_type = $wealth->wealthType->id;
                $wealth->load(['file', 'actions', 'indicators.qualityLabel']);

                $datas = [
                    'wealth' => $wealth,
                    'duplicate' => false,
                    'whoShouldSee' => $wealth->wealthType->name,
                    'unit' => $wealth->unit->id,
                    'granularity' => ['type' => $wealth->granularity['type']],
                ];

                // Attachement
                if (!is_null($wealth->attachment)) {
                    $datas['attachment'] = $wealth->attachment;
                }
            }
            else {
                // duplication
                $replicated = $wealth->replicate([
                    'id',
                    'conformity_level',
                    'validity_date',
                ]);
                $replicated->parent_id = $wealth->id;
                $replicated['indicators'] = $wealth->indicators;
                $replicated['tags'] = $wealth->tags;
                $replicated['actions'] = $wealth->actions;

                $datas = [
                    'wealth' => $replicated,
                    'duplicate' => true,
                    'whoShouldSee' => $wealth->wealthType->name,
                ];
            }
        }

        // 3) Précharge tous les labels qualité avec leurs critères et indicateurs
        $qualityLabels = QualityLabel::with(['criterias.indicators'])
            ->orderBy('name')
            ->get();

        $datas['qualityLabels'] = $qualityLabels;

        // POUR CHAQUE CRITÈRE, préparer une collection "rows_{critère_id}"
        foreach ($qualityLabels as $ql) {
            foreach ($ql->criterias as $crit) {
                $rows = $crit->indicators
                    ->sortBy('number')
                    ->map(function (Indicator $i) use ($wealth) {
                        $attached = $wealth->indicators->contains($i->id);
                        $pivot    = $wealth->indicators
                            ->firstWhere('id', $i->id)?->pivot;

                        return (object) [
                            'id'           => $i->id,
                            'number'       => $i->number,
                            'label'        => $i->label,
                            'attached'     => $attached,
                            'is_essential' => $pivot->is_essential ?? false,
                        ];
                    });

                // Injecte dans le repo sous la clé rows_{id}
                $datas['indicators_crit_' . $crit->id] = $rows;
            }
        }

        return $datas;
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->wealth->exists ? __('wealth_edit :name', ['name' => $this->wealth->name]) : __('wealth_create');
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('wealth_description');
    }

    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.quality.wealth.edit',
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        if ($this->wealth->exists || $this->duplicate) {
            //update or duplicate
            $saveParams = [
                "duplicate" => $this->duplicate,
            ];
        } else {
            //create
            $saveParams = [];
        }
        return [
            Link::make(__('Cancel'))
                ->icon('action-undo')
                ->route('platform.quality.wealths')
                ->class("cancel-btn"),

            Button::make('Save', __('Save'))
                ->icon('check')
                ->method('save', $saveParams),

            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('wealth_remove_confirmation'))
                ->method('remove', [
                    'wealth' => $this->wealth,
                ])
                ->canSee($this->wealth->exists),
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
            Layout::tabs(
                [
                    __('wealth') => EditLayout::class,
                    __('wealth_element') => AttachmentListener::class,
                    __('wealth_relation') => RelationsLayout::class,
                    __('indicators') => Layout::tabs(
                        // Premier niveau : QualityLabel
                        QualityLabel::with(['criterias.indicators'])
                            ->orderBy('name')
                            ->get()->mapWithKeys(fn($ql) => [
                                // Sous-onglets : critères du label
                                '<span title="'.$ql->description.'">'.$ql->label.'</span>' => Layout::tabs(
                                    $ql->criterias->sortBy('order')->mapWithKeys(
                                        fn($crit) => [
                                            '<span title="'.$crit->description.'">'.$crit->label.'</span>' =>
                                                Layout::table("indicators_crit_{$crit->id}", [
                                                    TD::make('attached', __('Associer'))
                                                        ->render(fn($r) => new HtmlString('
                                                            <div>
                                                                '.Switcher::make("wealth[indicators][{$r->id}][attached]")
                                                                    ->sendTrueOrFalse()
                                                                    ->value($r->attached)
                                                                    ->id("attached_{$r->id}")
                                                                    ->toHtml().'
                                                            </div>
                                                            <script>
                                                                setTimeout(function () {
                                                                    const attached = document.getElementById("attached_'.$r->id.'");
                                                                    const essential = document.getElementById("is_essential_'.$r->id.'");

                                                                    if (attached && essential) {
                                                                        const toggleEssential = () => {
                                                                            const wrapper = essential.closest("label");
                                                                            if (wrapper) {
                                                                                wrapper.classList.toggle("disabled", !attached.checked);
                                                                            }
                                                                            essential.disabled = !attached.checked;
                                                                        };

                                                                        toggleEssential(); // état initial
                                                                        attached.addEventListener("change", toggleEssential);
                                                                    }
                                                                }, 100);
                                                            </script>
                                                        ')),

                                                    TD::make('number', __('Numéro'))
                                                        ->render(fn($r) => $r->number),

                                                    TD::make('label', __('Indicateur'))
                                                        ->render(fn($r) =>
                                                            new HtmlString(
                                                                "<span title=\"".e($r->label)."\">".
                                                                e(Str::limit($r->label, 100))."</span>"
                                                            )
                                                        ),

                                                    TD::make('is_essential', __('Essentielle ?'))
                                                        ->render(
                                                            fn($r) => Switcher::make("wealth[indicators][{$r->id}][is_essential]")
                                                                ->sendTrueOrFalse()
                                                                ->value($r->is_essential)
                                                                ->id("is_essential_{$r->id}")
                                                        ),
                                                ]),
                                        ]
                                    )->all()
                                )
                            ])->all()
                    ),
                    __('details') => [GranularityListener::class, UnitListener::class],
                ]
            )->activeTab(__('wealth')),
        ];
    }

    /**
     * send data to attachment listener
     * @param [type] $payload
     * @return array
     */
    public function asyncAttachmentData($payload)
    {
        //get wealthTypeName according to id
        $type = WealthType::find($payload['wealth_type']);

        if (!is_null($payload['id']) || $payload['id'] != "") {
            $wealth = Wealth::find($payload['id']);
        } else {
            $wealth = new Wealth();
        }
        return [
            'wealth' => $wealth,
            'whoShouldSee' => $type->name,
        ];
    }

    /**
     * send data to granularity listener
     * @param [type] $payload
     * @return array
     */
    public function asyncGranularity($payload, Request $request)
    {
        //wealth to update
        if (!is_null($payload['id']) || $payload['id'] != "") {
            $wealth = Wealth::find($payload['id']);
        } else {
            $wealth = new Wealth();
        }

        $type = "";
        if (isset($payload['granularity']) && ($payload['granularity']['type'] ?? false)) {
            $type = $payload['granularity']['type'];
        }
        return [
            "wealth" => $wealth,
            "granularity" => [
                "type" => $type
            ]
        ];
    }

    /**
     * send data to unit listener
     * @param [type] $payload
     * @return array
     */
    public function asyncUnit($payload, Request $request)
    {
        if (!is_null($payload['id']) || $payload['id'] != "") {
            $wealth = Wealth::find($payload['id']);
        } else {
            $wealth = new Wealth();
        }
        return [
            "wealth" => $wealth,
            "unit" => isset($payload['unit']) ? $payload['unit'] : null,
        ];
    }

    /**
     * @param Wealth    $wealth
     * @param WealthRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Wealth $wealth, WealthRequest $request)
    {
        //les controles sont dans WealthRequest
        // on met à jour wealth pour clore et on enregistre la nouvelle
        if ($request->query('duplicate')) {
            # clone de l'éxistant
            $replicated = $wealth->replicate([
                'id',
                'archived_at',
                'conformity_level',
                'validity_date'
            ]);
            $replicated->parent_id = $wealth->id;

            # archivage de l'ancien
            $wealth->archived_at = Carbon::now();
            $wealth->conformity_level = 0;
            $wealth->save();

            # remplacement de l'ancien par le nouveau
            $wealth = $replicated;
        }

        //Datas from request
        $wealthData = $request->input('wealth', []);

        //Attachments
        //NOTE : ajouter un controle sur la validité de l'url
        $fileToUpload = null;
        if ($request->has('attachment')) {
            //data's attachment
            $attachments = $request->all('attachment')['attachment'];
            $dataAttachment = new Collection($attachments);
            if (isset($attachments['file'])) {
                $dataAttachment['file'] = ["type" => 'drive'];
                $fileToUpload = $attachments['file'];
            } else {
                if ($wealth->file) {
                    Toast::info('le fichier lié à cette preuve a été supprimé');
                }
            }
            $wealth->attachment = $dataAttachment;
        }

        //Create Wealth model
        $wealth
            ->fill($wealthData);

        //with Wealth type
        if (isset($wealthData['wealth_type'])) {
            $wealth
                ->wealthType()->associate($wealthData['wealth_type']);
        }
        //with unit
        if (isset($wealthData['unit'])) {
            $wealth
                ->unit()->associate($wealthData['unit']);
        }

        Wealth::withoutSyncingToSearch(function () use ($wealth) {
            $wealth->save();
        });

        // —————————————————————————————————————————
        // Mise à jour du pivot wealths_indicators
        // —————————————————————————————————————————
        $raw = $wealthData['indicators'] ?? [];

        // On préparera un tableau [ indicateur_id => ['is_essential'=>bool], ... ]
        $sync = [];

        foreach ($raw as $id => $flags) {
            // Si l’utilisateur a décoché “Associer”, on ignore cet indicateur
            if (empty($flags['attached'])) {
                continue;
            }

            // Sinon, on l’attache avec le bon flag
            $sync[(int)$id] = [
                'is_essential' => !empty($flags['is_essential']),
            ];
        }

        // Synchronise la relation many‑to‑many en passant par le pivot
        $wealth->indicators()->sync($sync);

        //Actions
        if (isset($wealthData['actions'])) {
            $actions = Action::find($wealthData['actions']);
        } else {
            $actions = [];
        }
        if (empty($actions)) {
            $wealth->actions()->detach();
        } else {
            $wealth->actions()->sync($actions);
        }

        //Tags
        if (isset($wealthData['tags'])) {
            $tags = Tag::find($wealthData['tags']);
        } else {
            $tags = [];
        }
        if (empty($tags)) {
            $wealth->tags()->detach();
        } else {
            $wealth->tags()->sync($tags);
        }

        //upload file and save in db
        if (isset($fileToUpload)) {
            $fileId = $this->saveFile($fileToUpload, $wealth);
            if ($fileId) {
                FileWealth::updateOrCreate(
                    ['wealth_id' => $wealth->id],
                    ['file_id' => $fileId]
                );
            }
            else {
                Toast::error(__('File_not_uploaded'));
            }
        }

        $wealth->save();

        Toast::success(__('Wealth_was_saved'));

        return redirect()->route('platform.quality.wealths');
    }

    /**
     * @param Wealth $wealth
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function remove(Wealth $wealth)
    {
        //DOC: Delete and relationships
        $wealth->actions()->detach();
        $wealth->tags()->detach();
        $wealth->indicators()->detach();

        $wealth->delete();

        Toast::success(__('Wealth_was_removed'));

        return redirect()->route('platform.quality.wealths');
    }

    /**
     * @param UploadedFile $file
     *
     * @throws \Exception
     *
     * @return String
     *
     */
    public function saveFile(UploadedFile $file, Wealth $wealth)
    {
        //DOC: Store file on google drive

        // get unit
        $unit = $wealth->unit->label;
        $unitDirectoryId = $this->getDirectoryId($this->formatDirName($unit));

        //try to stor
        try {
            $res = $file->storeAs($unitDirectoryId, $file->getClientOriginalName());
        } catch (\Throwable $th) {
            Toast::error(__('File_not_uploaded'));
            return false;
        }

        //gdrive meta datas
        $info = $this->getMetaData($res);
        $sharedLink = $this->formatSharedLink($info['path']);

        // save in db
        $fileToStore = new FileModel();
        $fileToStore->fill([
            'original_name' => $info['name'],
            'gdrive_shared_link' => $sharedLink,
            'gdrive_path_id' => $info['path'],
            'mime_type' => $info['mimetype'],
            'size' => $info['size'],
            'user_id' => Auth::id(),
        ])->save();

        Toast::success(__('file_is_added'));

        return $fileToStore->id;
    }

    /**
     * @param Wealth $wealth
     *
     * @throws \Exception
     *
     *
     */
    public function removeFile(Wealth $wealth, WealthRequest $request)
    {
        // DOC: Remove Files
        $action = $request->query("action");
        $file = $wealth->file;

        if($file) {
            switch ($action) {

                    //Archiving
                case 'archive':
                    //move file in archive directory in Qleiade
                    $archId = $this->getDirectoryId('archive');
                    $archDirId = $this->getDirectoryId($this->formatDirName($wealth->unit->label), $archId);
                    $newFilePath = $archDirId . "/" . $file->original_name;
                    Storage::cloud()->move($file->gdrive_path_id, $newFilePath);

                    //update wealth
                    FileWealth::where('wealth_id', $wealth->id)->delete();
                    $wealth->attachment = null;
                    $wealth->save();

                    //update file model
                    $file->archived_at = now();
                    $file->gdrive_shared_link = null;
                    $file->gdrive_path_id = $newFilePath;
                    $file->save();

                    Toast::success(__('file_archived'));
                    break;

                    // Archiving with delete file on drive
                case 'logical':
                    //delete file in gDrive
                    Storage::cloud()->delete($file->gdrive_path_id);

                    //update wealth
                    FileWealth::where('wealth_id', $wealth->id)->delete();
                    $wealth->attachment = null;
                    $wealth->save();

                    //soft delete (complet deleted_at columns)
                    $file->delete();

                    Toast::success(__('file_deleted_logic'));
                    break;

                    //Eradicate file
                case 'eradicate':
                    //delete file definitly in g drive
                    Storage::cloud()->delete($file->gdrive_path_id);

                    //update wealth
                    FileWealth::where('wealth_id', $wealth->id)->delete();
                    $wealth->attachment = null;
                    $wealth->save();

                    //delet file definitly in
                    $file->forceDelete();

                    Toast::success(__('file_deleted_permanently'));
                    break;

                default:
                    Toast::warning('File not deleted there are no actions', __('file_not_deleted'));
                    break;
            }
        }
    }

    /**
     * removeAttachment
     *
     * @param  mixed $wealth
     * @return void
     */
    public function removeAttachment(Wealth $wealth)
    {
        //DOC: Remove Attachment
        $wealth->attachment = null;
        $wealth->save();
    }

    public function editAttachment(Wealth $wealth) {

    }
}
