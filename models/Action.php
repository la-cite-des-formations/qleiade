<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Action extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'action';

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
     * wealths
     *
     * @return void
     */
    public function wealths()
    {
        return $this->belongsToMany(
            Wealth::class,
            "wealths_actions",
            "action_id",
            "wealth_id"
        );
    }

    /**
     * stage
     *
     * @return Stage
     */
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    /**
     * unit
     *
     * @return Collection
     */
    public function unit()
    {
        return $this->belongsToMany(
            Unit::class,
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
        return $this->attributes['order'] . ' - ' . $this->attributes['label'] . ' (' . $this->stage->label . ')';
    }
}
