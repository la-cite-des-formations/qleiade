<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class FileWealth extends Model
{
    // Indiquer le nom de la table
    protected $table = 'file_wealths';

    // Indiquer que la clé primaire n'est pas auto-incrémentée
    public $incrementing = false;

    // Indiquer la clé primaire
    protected $primaryKey = 'wealth_id';

    // On désactive les timestamps (created_at/updated_at) pour cette table
    public $timestamps = false;

    /**
     * wealth
     *
     * @return Relation
     */
    public function wealth(): Relation
    {
        return $this->belongsTo(Wealth::class, 'wealth_id');
    }

    /**
     * file
     *
     * @return Relation
     */
    public function file(): Relation
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
