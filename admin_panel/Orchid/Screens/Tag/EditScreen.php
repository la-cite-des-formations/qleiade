<?php

namespace Admin\Orchid\Screens\Tag;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Models\Tag;
use Admin\Orchid\Layouts\Tag\EditLayout;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;

//DOC NEW ORCHID FORM: add new screen (php artisan orchid:screen NameScreen)

class EditScreen extends Screen
{

    /**
     * @var Tag
     */
    public $tag;

    /**
     * Query data.
     *
     * @param Tag
     * @return array
     */
    public function query(Tag $tag): iterable
    {
        return [
            'tag' => $tag,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->tag->exists ? __('tag_edit :label', ['label' => $this->tag->label]) : __('tag_create');
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('tag_description');
    }

    //DOC: orchid add permission to a screen
    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.quality.tags.edit',
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
                ->route('platform.quality.tags'),

            Button::make('Save', __('Save'))
                ->icon('check')
                ->confirm(__('tag_save_confirmation'))
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('tag_remove_confirmation'))
                ->method('remove', [
                    'tag' => $this->tag,
                ])
                ->canSee($this->tag->exists),
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
     * @param Tag    $tag
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Tag $tag, Request $request)
    {
        $request->validate([
            // 'tag.label' => "required|regex:/^[a-zA-Z0-9\s]+$/"
            'tag.label' => "required"
        ]);

        //Datas from request
        $tagData = $request->all('tag')['tag'];
        // format name code

        $tagData["name"] = Str::slug($tagData["label"]);

        //Create Tag model
        $tag->fill($tagData)
            ->save();

        Toast::success(__('Tag_was_saved'));

        return redirect()->route('platform.quality.tags');
    }

    /**
     * @param Tag $tag
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function remove(Tag $tag)
    {
        $tag->delete();

        Toast::success(__('Tag_was_removed'));

        return redirect()->route('platform.quality.tags');
    }
}
