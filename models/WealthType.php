<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class WealthType extends Model
{
    use HasFactory, AsSource, Filterable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wealth_type';

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
     * wealths
     *
     * @return void
     */
    public function wealths()
    {
        return $this->hasMany(Wealth::class);
    }
}
