<?php

namespace Admin\Orchid\Screens\Wealth;

use Illuminate\Support\Facades\Auth;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;

use Models\Wealth;
use App\Http\Traits\WithAttachments;


class DisplayScreen extends Screen
{
    use WithAttachments;
    /**
     * @var Wealth
     */
    public $wealth;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Wealth $wealth): iterable
    {
        return [
            'wealth' => $wealth
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        $name = $this->wealth->name;
        if (!is_null($this->wealth->archived_at)) {
        }
        return $name;
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Return'))
                ->icon('action-undo')
                ->route('platform.quality.wealths')
                ->class("cancel-btn"),

            Link::make(__('Edit'))
                ->route('platform.quality.wealth.edit', ["wealth" => $this->wealth->id, "duplicate" => false])
                ->icon('pencil')
                ->canSee(Auth::user()->hasAccess('platform.quality.wealth.create') && is_null($this->wealth->archived_at))
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
            Layout::view(
                'wealth.show.wealth',
                [
                    'wealth' => $this->wealth,
                    'emptyAttachments' => $this->isEmptyAttachments($this->wealth->attachment)
                ]
            )
        ];
    }
}
