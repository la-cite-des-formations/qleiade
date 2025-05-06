<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Indicator extends Model
{
    use HasFactory, AsSource, Filterable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'indicator';


    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
        'description',
        //numéro indicateur 1 à 37
        'number',
        'conformity_level_expected'
    ];

    /**
     * wealths
     *
     * @return Collection
     */
    public function wealths()
    {
        return $this->belongsToMany(
            Wealth::class,
            "wealths_indicators",
            "indicator_id",
            "wealth_id"
        );
    }

    //NOTE peut être faut il supprimer ce lien direct mais attention ça casse tout le crud
    /**
     * qualityLabel
     *
     * @return QualityLabel
     */
    public function qualityLabel()
    {
        return $this->belongsTo(QualityLabel::class);
    }

    /**
     * qualityLabel
     *
     * @return Criteria
     */
    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }

    /**
     * getFullAttribute
     *
     * @return string
     */
    public function getFullAttribute(): string
    {
        return $this->criteria->order . '.' . $this->attributes['number'] . " . " . $this->attributes['label'];
    }
}
