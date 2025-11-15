<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programmation extends Model
{
    use HasFactory;

    protected $table = 'programmation';
    public $incrementing = false; // Composite primary key
    // protected $primaryKey and protected $keyType are omitted for composite primary keys

    protected $fillable = [
        'code_ec',
        'num_salle',
        'code_pers',
        'date',
        'heure_debut',
        'heure_fin',
        'nbre_heure',
        'status',
    ];

    public $timestamps = true;
}
