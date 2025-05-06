<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Models\Audit;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\AttachOne;

use Orchid\Screen\AsSource;

class QualityLabel extends Model
{
    use HasFactory, AsSource, Attachable;

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
     * @return Collection
     */
    public function indicators()
    {
        return $this->hasMany(Indicator::class);
    }

    /**
     * Criterias
     * Un label qualité a plusieurs critère
     * @return Collection
     */
    public function criterias()
    {
        return $this->hasMany(Criteria::class);
    }
    /**
     * audits
     *
     * @return void
     */
    public function audits()
    {
        return $this->hasMany(Audit::class);
    }
}
