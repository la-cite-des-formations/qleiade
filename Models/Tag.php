<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;

class Tag extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tag';

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
     * @return Relation
     */
    public function wealths(): Relation
    {
        return $this->belongsToMany(
            Wealth::class,
            "wealths_tags",
            "tag_id",
            "wealth_id"
        );
    }
}
