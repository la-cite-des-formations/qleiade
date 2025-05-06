<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Orchid\Screen\AsSource;

class Tag extends Model
{
    use HasFactory, AsSource;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tag';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
        'description',
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'name',
    ];

    /**
     * @var array
     */
    protected $allowedFilters = [
        'name',
    ];

    /**
     * wealths
     *
     * @return void
     */
    public function wealths()
    {
        return $this->belongsToMany(
            Wealth::class,
            "wealths_tags",
            "tag_id",
            "wealth_id"
        );
    }
}
