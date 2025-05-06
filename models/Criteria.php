<?php

namespace Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        'name',
        'label',
        'order',
        'description',
    ];

    /**
     * Indicators
     * Un critère a plusieurs indicateurs
     * @return Collection
     */
    public function indicators()
    {
        return $this->hasMany(Indicator::class);
    }

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
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeLabel($query, $v)
    {
        return $query->where('quality_label_id', $v);
    }
}
