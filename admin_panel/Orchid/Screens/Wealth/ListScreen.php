<?php

namespace Admin\Orchid\Screens\Wealth;

use Admin\Orchid\Layouts\Parts\SearchLayout;
use Admin\Orchid\Layouts\Wealth\ListLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;

use Models\Wealth;

use Admin\Orchid\Layouts\Wealth\SearchListener;
use Illuminate\Support\Facades\Log;
use Models\WealthType;
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
        $payload = array_merge(["sort" => request('sort')], request('search') ?? []);

        return $this->asyncFilterList($payload);
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
        return [
            SearchLayout::class,
            SearchListener::class,
        ];
    }

    /**
     * Asynchronous filter list method.
     *
     * @param  array  $payload
     * @return array
     */
    public function asyncFilterList($payload)
    {
        Log::info('asyncFilterList called', $payload);

        // dd($payload);

        $validator = Validator::make($payload, [
            'keyword' => 'nullable|string|max:255',
            'units' => 'nullable|array',
            'indicators'=> 'nullable|array',
            'conformity' => 'nullable|in:0,essentielle,complémentaire',
            'wealth_type' => 'nullable|numeric',
            'sort' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            Log::info('asyncFilterList : error 403');
            abort(403);
        }

        $data = $validator->validated();
        $keyWord = $data['keyword'] ?? '';
        $filters = [];

        if (! empty($data['units'][0])) {
            $ids = implode(',', $data['units']);
            $filters[] = "unit.id IN [{$ids}]";
        }

        if (! empty($data['indicators'][0])) {
            $ids = implode(',', $data['indicators']);
            $filters[] = "indicators.id IN [{$ids}]";
        }

        if (! empty($data['conformity'])) {
            $filters[] = "conformity_level = '{$data['conformity']}'";
        }

        if (! empty($data['wealth_type'])) {
            $wt = WealthType::find($data['wealth_type']);
            if ($wt) {
                $filters[] = "wealth_type = '{$wt->label}'";
            }
        }

        $filter = implode(' AND ', $filters);

        // Récupération des paramètres de recherche et de tri
        $rawSort = $data['sort'] ?? NULL;

        // Détection du signe - pour le sens
        $sort  = null;
        $order = 'asc';

        if ($rawSort) {
            if (str_starts_with($rawSort, '-')) {
                $sort  = substr($rawSort, 1);
                $order = 'desc';
            } else {
                $sort  = $rawSort;
                $order = 'asc';
            }
        }

        try {
            $wealths = Wealth::search($keyWord, function ($meilisearch, $query, $options) use ($filter, $sort, $order) {
                if ($filter) {
                    $options['filter'] = $filter;
                }

                if ($sort) {
                    $options['sort'] = ["{$sort}:{$order}"];
                }

                return $meilisearch->search($query, $options);
            })
            ->paginate(10)
            ->withPath(route('platform.quality.wealths'));

        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            abort(500, "connexion_error");
        }
        return ['wealths' => $wealths];
    }

    /**
     * Remove a wealth.
     *
     * @param  Request  $request
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
