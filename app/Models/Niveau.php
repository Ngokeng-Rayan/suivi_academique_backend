<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    use HasFactory;

    protected $table = 'niveau';
    protected $primaryKey = 'code_niveau';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'label_niveau',
        'desc_niveau',
        'code_filiere',
    ];

    public $timestamps = true;
}
