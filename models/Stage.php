<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Screen\AsSource;

class Stage extends Model
{
    use HasFactory, AsSource;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
        'description',
    ];

    /**
     * actions
     * Une étape a plusieurs actions
     * @return HasMany
     */
    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }
}
