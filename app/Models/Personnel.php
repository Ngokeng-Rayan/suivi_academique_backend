<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'personnel';
    protected $primaryKey = 'code_pers';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code_pers',
        'nom_pers',
        'prenom_pers',
        'sexe_pers',
        'phone_pers',
        'login_pers',
        'pwd_pers',
        'type_pers',
    ];

    public $timestamps = true;
}
