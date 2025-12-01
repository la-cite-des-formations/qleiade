<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Indicator extends Model
{
    use HasFactory;

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
        'criteria_id',
        'name',
        'label',
        'description',
        //numéro indicateur 1 à 37
        'number',
        'conformity_level_expected'
    ];

    /**
     * Filtre les indicateurs par Label Qualité et les trie.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $dependency // Les valeurs des champs dont on dépend
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByQualityLabelAndSort($query, $dependency = [])
    {
        Log::info('--- DÉPENDANCE INDICATEUR REÇUE ---', $dependency);
        // 1. On récupère l'ID du label qualité depuis le champ dépendant
        // (le '?' est important si le champ est vide)
        $qualityLabelId = $dependency['search.quality_label'] ?? null;

        // 2. On filtre par le Label Qualité s'il est sélectionné
        if ($qualityLabelId) {
            $query->whereHas('criteria', function ($q) use ($qualityLabelId) {
                $q->where('quality_label_id', $qualityLabelId);
            });
        }

        // 3. On applique le TRI NUMÉRIQUE (le plus important)
        // On doit join() pour pouvoir trier sur la colonne d'une autre table
        $query->join('criteria', 'indicator.criteria_id', '=', 'criteria.id')
            ->select('indicator.*') // Très important pour éviter les conflits d'ID
            ->orderBy('criteria.order', 'asc') // Tri N°1
            ->orderBy('indicator.number', 'asc'); // Tri N°2

        return $query;
    }

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
        )
        ->withPivot('is_essential')
        ->withTimestamps();
    }

    /**
     * qualityLabel
     *
     * @return QualityLabel
     */
    public function qualityLabel()
    {
        return $this->hasOneThrough(
            QualityLabel::class, Criteria::class,
            'id', 'id',
            'criteria_id', 'quality_label_id'
        );
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
