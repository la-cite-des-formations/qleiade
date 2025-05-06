<?php

namespace Admin\Orchid\Layouts\Tag;

use Illuminate\Support\Facades\Auth;

use Models\Tag;
use Illuminate\Support\Str;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;

class ListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'tags';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make(__('label'))
                ->sort()
                ->render(function (Tag $tag) {
                    return $tag->label;
                }),

            TD::make(__('description'))
                ->sort()
                ->render(function (Tag $tag) {
                    //NOTE : add function getFirstSentenceOfHtml($html) to a trait if anothers
                    $truncatedDesc = Str::before(Str::after($tag->description, '<p>'), '</p>') . ' ...';

                    return $truncatedDesc;
                }),


            TD::make(__('Actions_form'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Tag $tag) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.quality.tags.edit', $tag->id)
                                ->icon('pencil'),

                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->confirm(__('tag_remove_confirmation'))
                                ->method('remove', [
                                    'id' => $tag->id,
                                ]),
                        ])
                        ->canSee(Auth::user()->hasAccess('platform.quality.tags.edit'));
                }),
        ];
    }
}
