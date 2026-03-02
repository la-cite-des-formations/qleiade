<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;

class Criteria extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'criteria';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quality_label_id',
        'name',
        'label',
        'order',
        'description',
    ];

    /**
     * Indicators
     * Un critère a plusieurs indicateurs
     * @return Relation
     */
    public function indicators(): Relation
    {
        return $this->hasMany(Indicator::class);
    }

    /**
     * qualityLabel
     *
     * @return Relation
     */
    public function qualityLabel(): Relation
    {
        return $this->belongsTo(QualityLabel::class);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeLabel(Builder $query, $v): Builder
    {
        return $query->where('quality_label_id', $v);
    }
}
