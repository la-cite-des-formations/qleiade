<?php

namespace Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;

class Unit extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'unit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'manager_name',
        'label',
        'description',
    ];

    /**
     * wealths
     *
     * @return Relation
     */
    public function wealths(): Relation
    {
        return $this->hasMany(Wealth::class);
    }

    /**
     * wealths
     *
     * @return Relation
     */
    public function users(): Relation
    {
        return $this->belongsToMany(
            User::class,
            "users_unit",
            "user_id",
            "unit_id"
        );
    }

    /**
     * wealths
     *
     * @return Relation
     */
    public function actions(): Relation
    {
        return $this->belongsToMany(
            Action::class,
            "actions_unit",
            "action_id",
            "unit_id"
        );
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeByAlphaSort(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * @return string
     */
    public function getFullAttribute(): string
    {
        return sprintf('%s - %s', $this->name, $this->label);
    }
}
