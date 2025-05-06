<?php

namespace Admin\Orchid\Screens\Wealth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;

use Models\Wealth;
use Models\Unit;

use Admin\Orchid\Layouts\Parts\SearchLayout;
use Admin\Orchid\Layouts\Wealth\ListListener;

class ListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'wealths' => Wealth::with(['indicators', 'unit', 'wealthType'])
                ->filters()
                ->paginate(10),
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('wealths');
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return __('wealths_list_description');
    }

    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.quality.wealths',
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
            Link::make(__('Add'))
                ->icon('plus')
                ->route('platform.quality.wealth.create')
                //it works
                ->canSee(Auth::user()->hasAccess('platform.quality.wealth.create')),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $searchForm = new SearchLayout();
        return [
            $searchForm->title(__('search')),
            ListListener::class,
        ];
    }

    /**
     *
     * @return string[]
     */
    public function asyncFilterList($payload)
    {
        $validator = Validator::make($payload, [
            'keyword' => "nullable|regex:/^[-'a-zA-ZÀ-ÖØ-öø-ÿ]+$/",
            'unit' => 'nullable|numeric',
            'archived' => 'nullable'
        ]);

        if ($validator->fails()) {
            abort(403);
        }

        $data = $validator->validated();

        $keyWord = $data['keyword'];
        $filter = "";
        if (isset($data['archived'])) {
            // filter archived
            $filter .= "archived = true";
        }

        if (isset($data['archived']) && (isset($data['unit']) && !is_null($data['unit']))) {
            $filter .= ' AND ';
        }

        $unitLabel = '';
        if (isset($data['unit']) && !is_null($data['unit'])) {
            $unitLabel = Unit::find($data['unit'])->label;
            $filter .= "unit.label = '" . $unitLabel . "'";
        }
        try {
            $wealths = Wealth::search($keyWord, function ($meilisearch, $query, $options) use ($filter) {
                $options['filter'] = $filter;

                return $meilisearch->search($query, $options);
            })->get();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            abort(500, "connexion_error");
        }
        return [
            'wealths' => $wealths,
        ];
    }

    /**
     * remove
     *
     * @param  Request $request
     * @return void
     */
    public function remove(Request $request): void
    {
        $wealth = Wealth::findOrFail($request->get('id'));

        $wealth->actions()->detach();
        $wealth->tags()->detach();
        $wealth->indicators()->detach();

        $wealth->delete();
        Toast::info(__('Wealth_was_removed'));
    }
}
