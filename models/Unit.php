<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Unit extends Model
{
    use HasFactory, AsSource, Filterable;

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
     * @return void
     */
    public function wealths()
    {
        return $this->hasMany(Wealth::class);
    }

    /**
     * wealths
     *
     * @return Collection
     */
    public function users()
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
     * @return Collection
     */
    public function actions()
    {
        return $this->belongsToMany(
            Action::class,
            "actions_unit",
            "action_id",
            "unit_id"
        );
    }

    /**
     * @return string
     */
    public function getFullAttribute(): string
    {
        return $this->attributes['name'] . ' - ' . $this->attributes['label'];
    }
}
