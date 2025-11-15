<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec extends Model
{
    use HasFactory;

    protected $table = 'ec';
    protected $primaryKey = 'code_ec';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'label_ec',
        'desc_ec',
        'code_ue',
    ];

    public $timestamps = true;
}
