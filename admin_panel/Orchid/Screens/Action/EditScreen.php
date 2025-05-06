<?php

namespace Admin\Orchid\Screens\Action;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Models\Action;
use Models\Unit;
use Admin\Orchid\Layouts\Action\EditLayout;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;

//DOC NEW ORCHID FORM: add new screen (php artisan orchid:screen NameScreen)

class EditScreen extends Screen
{

    /**
     * @var Action
     */
    public $action;

    /**
     * Query data.
     *
     * @param Action
     * @return array
     */
    public function query(Action $action): iterable
    {
        return [
            'action' => $action,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->action->exists ? __('action_edit :label', ['label' => $this->action->label]) : __('action_create');
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('action_description');
    }

    //DOC: orchid add permission to a screen
    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.quality.actions.edit',
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
                ->route('platform.quality.actions'),

            Button::make('Save', __('Save'))
                ->icon('check')
                ->confirm(__('action_save_confirmation'))
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('action_remove_confirmation'))
                ->method('remove', [
                    'action' => $this->action,
                ])
                ->canSee($this->action->exists),
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
     * @param Action    $action
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Action $action, Request $request)
    {
        // $request->validate([
        //     'action.label' => "required|regex:/^[a-zA-Z0-9\s]+$/"
        // ]);

        //Datas from request
        $actionData = $request->all('action')['action'];
        // format name code
        $actionData["name"] = Str::slug($actionData["label"]);

        //Create Action model
        $action->fill($actionData)
            ->stage()
            ->associate($actionData['stage'])
            ->save();
        if (isset($actionData['unit'])) {
            $proc = Unit::find($actionData['unit']);
            $action->unit()->sync($proc);
        }

        Toast::success(__('Action_was_saved'));

        return redirect()->route('platform.quality.actions');
    }

    /**
     * @param Action $action
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function remove(Action $action)
    {
        $action->delete();

        Toast::success(__('Action_was_removed'));

        return redirect()->route('platform.quality.actions');
    }
}
