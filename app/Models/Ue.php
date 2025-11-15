<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ue extends Model
{
    use HasFactory;

    protected $table = 'ue';
    protected $primaryKey = 'code_ue';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code_ue',
        'label_ue',
        'desc_ue',
        'code_niveau',
    ];

    public $timestamps = true;
}
