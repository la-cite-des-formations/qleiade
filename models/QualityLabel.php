<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Models\Audit;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\AttachOne;

// use Orchid\Screen\AsSource;

class QualityLabel extends Model
{
    use HasFactory, Attachable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quality_label';

    /**
     * @AttachOne("image")
     */
    public $attachment;

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
     * @return HasManyThrough
     */
    public function indicators(): HasManyThrough
    {
        return $this->hasManyThrough(Indicator::class, Criteria::class);
    }

    /**
     * Criterias
     * Un label qualité a plusieurs critère
     * @return HasMany
     */
    public function criterias(): HasMany
    {
        return $this->hasMany(Criteria::class);
    }
    /**
     * audits
     *
     * @return HasMany
     */
    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }
}
