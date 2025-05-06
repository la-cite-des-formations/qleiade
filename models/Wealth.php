<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Scout\Searchable;
use Laravel\Scout\EngineManager;

use Database\Factories\WealthFactory;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;


class Wealth extends Model
{
    use HasFactory, AsSource, Searchable, Filterable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wealth';


    //Scout functions
    /**
     * Get the engine used to index the model.
     *
     * @return \Laravel\Scout\Engines\Engine
     */
    public function searchableUsing()
    {
        return app(EngineManager::class)->engine('meilisearch');
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'wealths';
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->id;
    }

    /**
     * Get the key name used to index the model.
     *
     * @return mixed
     */
    public function getScoutKeyName()
    {
        return 'id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        //suivi de la preuve
        'tracking',
        // 0 a 99
        'conformity_level',
        //json
        'granularity',
        'validity_date',
        'archived_at',
        // json les visuelles de la preuve file, link, ypareo
        'attachment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'validity_date' => 'datetime',
        'archived_at' => 'datetime',
        'attachment' => 'array',
        'granularity' => 'array',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id',
        'name',
        'unit',
        'validity_date',
        'archived_at',
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'validity_date',
        'archived_at',
    ];


    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return WealthFactory::new();
    }

    /**
     * actions
     *
     * @return Collection
     */
    public function actions()
    {
        return $this->belongsToMany(
            Action::class,
            "wealths_actions",
            "wealth_id",
            "action_id"
        );
    }

    /**
     * wealthType
     *
     * @return WealthType
     */
    public function wealthType()
    {
        return $this->belongsTo(WealthType::class);
    }

    /**
     * indicators
     *
     * @return Collection
     */

    public function indicators()
    {
        return $this->belongsToMany(
            Indicator::class,
            "wealths_indicators",
            "wealth_id",
            "indicator_id"
        );
    }

    /**
     * files
     *
     * @return Collection
     */
    public function files()
    {
        return $this->belongsToMany(
            File::class,
            "wealths_files",
            "wealth_id",
            "file_id"
        );
    }

    //NOTE peut être faut il supprimer ce lien direct mais attention ça casse tout le crud
    /**
     * unit
     *
     * @return Collection
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * tags
     *
     * @return Collection
     */
    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            "wealths_tags",
            "wealth_id",
            "tag_id"
        );
    }

    // return one level of child items
    public function wealths()
    {
        return $this->hasMany(Wealth::class, 'parent_id');
    }

    // recursive relationship
    public function childWealths()
    {
        return $this->hasMany(Wealth::class, 'parent_id')->with('wealths');
    }

    // /**
    //  * Get the presenter for the model.
    //  *
    //  * @return WealthPresenter
    //  */
    // public function presenter()
    // {
    //     return new WealthPresenter($this);
    // }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        // $array = $this->toArray();

        $array = [
            "id" => $this->id,
            "name" => $this->name,
            "parent_id" => $this->parent_id,
            "granularity_type" => $this->granularity['type'],
            "granularity_id" => isset($this->granularity['id']) ? $this->granularity['id'] : null,
            "conformity_level" => $this->conformity_level == 100 ? "essentielle" : "complémentaire",
            "validity_date" => $this->validity_date ? $this->validity_date->toDateTimeString() : null,
            "created_at" => $this->created_at->toDateTimeString(),
            "archived" => !is_null($this->archived_at),
            "unit" => [
                "name" => $this->unit->name,
                "label" => $this->unit->label
            ],
            "wealth_type" => $this->wealthType->label,
            "indicators" => $this->indicators->map(function ($item, $key) {
                $value = [
                    "label" => $item["label"],
                    "quality_label" => $item['qualityLabel']->label,
                ];
                return $value;
            })->toArray(),
            "tags" => $this->tags->map(function ($item, $key) {
                $value = [
                    "label" => $item["label"],
                ];
                return $value;
            })->toArray(),
            "actions" => $this->actions->map(function ($item, $key) {
                $value = [
                    "label" => $item["label"],
                    "stage" => $item['stage']->label,
                ];
                return $value;
            })->toArray(),
        ];
        return $array;
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with(['indicators', 'tags', 'actions', 'wealthType', 'unit']);
    }
}
