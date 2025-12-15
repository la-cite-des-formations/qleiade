<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

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

    // Relation inverse (optionnelle, mais utile)
    public function wealth()
    {
        return $this->belongsTo(Wealth::class, 'wealth_id');
    }

    // Relation vers le fichier (optionnelle, mais utile)
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
