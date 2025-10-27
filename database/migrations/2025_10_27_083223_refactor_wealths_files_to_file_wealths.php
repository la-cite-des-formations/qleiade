<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // N'oublie pas d'importer DB

class RefactorWealthsFilesToFileWealths extends Migration
{
    /**
     * Le nom de l'ancienne table (actuelle).
     */
    protected $oldTable = 'wealths_files';

    /**
     * Le nom de la nouvelle table (d'extension).
     */
    protected $newTable = 'file_wealths';


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // -----------------------------------------------------------------
        // ETAPE 1 : NETTOYAGE DES DONNEES (Suppression des doublons)
        // -----------------------------------------------------------------
        // On ne peut pas créer une clé primaire sur 'wealth_id' s'il y a
        // des doublons. On garde le premier 'id' (le plus ancien)
        // pour chaque 'wealth_id' et on supprime les autres.

        // ATTENTION : Cette opération supprime des données de manière irréversible.
        // Fais une sauvegarde de ta BDD avant de lancer cette migration.

        DB::delete("
            DELETE FROM {$this->oldTable}
            WHERE id NOT IN (
                SELECT min_id FROM (
                    SELECT MIN(id) as min_id
                    FROM {$this->oldTable}
                    GROUP BY wealth_id
                ) as tmp
            )
        ");

        // -----------------------------------------------------------------
        // ETAPE 2 : Renommage de la table
        // -----------------------------------------------------------------
        Schema::rename($this->oldTable, $this->newTable);

        // -----------------------------------------------------------------
        // ETAPE 3 : Modification de la structure
        // -----------------------------------------------------------------
        Schema::table($this->newTable, function (Blueprint $table) {

            // On supprime l'ancienne clé primaire "id"
            $table->dropColumn('id');

            // On supprime les timestamps (inutiles sur cette table 1:1)
            $table->dropTimestamps();

            // On définit 'wealth_id' comme LA nouvelle clé primaire.
            // Cela force la contrainte 1-to-1.
            $table->primary('wealth_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // On fait l'inverse, dans l'ordre inverse

        // 1. On modifie la structure
        Schema::table($this->newTable, function (Blueprint $table) {
            // On supprime la clé primaire sur 'wealth_id'
            $table->dropPrimary(['wealth_id']);

            // On recrée la colonne 'id' auto-incrémentée
            $table->id()->first();

            // On recrée les timestamps
            $table->timestamps();
        });

        // 2. On renomme la table
        Schema::rename($this->newTable, $this->oldTable);
    }
}
