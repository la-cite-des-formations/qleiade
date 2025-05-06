<?php

namespace Admin\Orchid\Screens\Wealth;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Arr;

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
use Models\QualityLabel;
use Models\File as FileModel;
use App\Http\Traits\DriveManagement;
use App\Http\Traits\WithAttachments;
use App\Http\Requests\WealthRequest;

use Admin\Orchid\Layouts\Wealth\EditLayout;
use Admin\Orchid\Layouts\Wealth\RelationsLayout;
use Admin\Orchid\Layouts\Wealth\AttachmentListener;
use Admin\Orchid\Layouts\Wealth\GranularityListener;
use Admin\Orchid\Layouts\Wealth\UnitListener;
use Admin\Orchid\Layouts\Wealth\QualityLabelListener;
use Orchid\Support\Facades\Alert;

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

        //get whoShouldSee in session when wealth create validation fail
        $whoShouldSee = $request->session()->get('whoShouldSee');
        if (!is_null($whoShouldSee)) {
            $datas['whoShouldSee'] = $whoShouldSee;
        } else {
            $datas['whoShouldSee'] = "";
        }

        if (!$wealth->exists) {
            # create
            $datas['wealth'] = $wealth;
            $datas['duplicate'] = false;
        } else {
            # edit
            if (is_null($duplicate)) {
                # update
                $wealth->wealth_type = $wealth->wealthType->id;

                //load qualitylabel according to indicators
                if (count($wealth->indicators) > 0) {
                    $wealth->qualityLabel = $wealth->indicators[0]->qualityLabel->id;
                }

                if (count($wealth->files) >= 1) {
                    $wealth->file = $wealth->files[0];
                }
                $wealth->load("actions");

                //add value to data if you want to map values accross multiple layouts and listeners
                $datas = [
                    'wealth' => $wealth,
                    'duplicate' => false,
                    'whoShouldSee' => $wealth->wealthType->name,
                    'unit' => $wealth->unit->id,
                    'granularity' => ['type' => $wealth->granularity['type']],
                    'qualityLabel' => $wealth->indicators[0]->qualityLabel->id
                ];

                //add attachment if exists in db
                if (!is_null($wealth->attachment)) {
                    $datas['attachment'] = $wealth->attachment;
                }
            } else {
                # duplicate
                $replicatedWealth = $wealth->replicate([
                    'id',
                    'conformity_level',
                    'validity_date'
                ]);

                $replicatedWealth->parent_id = $wealth->id;
                $replicatedWealth['indicators'] = $wealth->indicators;
                $replicatedWealth['tags'] = $wealth->tags;
                $replicatedWealth['actions'] = $wealth->actions;
                $wealth = $replicatedWealth;
                $datas["wealth"] = $replicatedWealth;

                $datas['wealth'] = $replicatedWealth;
                $datas['duplicate'] = true;
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
                    __('wealth_relation') => [RelationsLayout::class],
                    __('details') => [GranularityListener::class, UnitListener::class, QualityLabelListener::class],
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
     * send data to qualitylabel listener
     * @param [type] $payload
     * @return array
     */
    public function asyncQualityLabel($payload, Request $request)
    {
        //wealth to update
        if (isset($payload['id']) && (!is_null($payload['id']) || $payload['id'] != "")) {
            $wealth = Wealth::find($payload['id']);
        } else {
            $wealth = new Wealth();
        }
        return [
            "wealth" => $wealth,
            "qualityLabel" => isset($payload['qualityLabel']) ? $payload['qualityLabel'] : null,
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
        $wealthData = $request->all('wealth')['wealth'];

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
                if (count($wealth->files) > 0) {
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

        \Models\Wealth::withoutSyncingToSearch(function () use ($wealth) {
            $wealth->save();
        });

        //DOC: update accross relationships
        if (isset($wealthData['indicators'])) {
            $indicators = Indicator::find($wealthData['indicators']);
        } else {
            $indicators = [];
        }
        if (empty($indicators)) {
            // $wealth->indicators()->detach();
        } else {
            $wealth->indicators()->sync($indicators);
        }

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
                $wealth->files()->sync($fileId);
            } else {
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
        foreach ($wealth->files as $file) {
            switch ($action) {

                    //Archiving
                case 'archive':
                    //move file in archive directory in Qleiade
                    $archId = $this->getDirectoryId('archive');
                    $archDirId = $this->getDirectoryId($this->formatDirName($wealth->unit->label), $archId);
                    $newFilePath = $archDirId . "/" . $file->original_name;
                    Storage::cloud()->move($file->gdrive_path_id, $newFilePath);

                    //update wealth
                    $wealth->files()->detach($file->id);
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
                    $wealth->files()->detach($file->id);
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
                    $wealth->files()->detach($file->id);
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
