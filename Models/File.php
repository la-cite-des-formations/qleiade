<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;

class File extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'files';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'archived_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'original_name',
        'gdrive_shared_link',
        'gdrive_path_id',
        'mime_type',
        'archived_at',
        'size',
        'user_id'
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
            "wealths_files",
            "file_id",
            "wealth_id"
        );
    }
}
