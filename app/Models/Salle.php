<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salle extends Model

{

    // Nom de la table associée

    protected $table = 'salle';

    // Clé primaire personnalisée

    protected $primaryKey = 'num_sale';

    // Indiquer que la clé primaire n'est pas auto-incrémentée

    public $incrementing = false;

    // Type de la clé primaire

    protected $keyType = 'string';

    // Colonnes autorisées pour la création/mise à jour en masse

    protected $fillable = [

        'num_sale',

        'contenance',

        'statut',

    ];

    // Active la gestion automatique des colonnes created_at et updated_at

    public $timestamps = true;

}
