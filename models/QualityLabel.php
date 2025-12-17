<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;

class QualityLabel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quality_label';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
        'image',
        'criterias_count_expected',
        'indicator_count_expected',
        'audit_frequency',
        'last_audit_date',
        'description',
    ];

    /**
     * indicators
     *
     * @return Relation
     */
    public function indicators(): Relation
    {
        return $this->hasManyThrough(Indicator::class, Criteria::class);
    }

    /**
     * Criterias
     * Un label qualité a plusieurs critère
     * @return Relation
     */
    public function criterias(): Relation
    {
        return $this->hasMany(Criteria::class);
    }
    /**
     * audits
     *
     * @return Relation
     */
    public function audits(): Relation
    {
        return $this->hasMany(Audit::class);
    }
}
