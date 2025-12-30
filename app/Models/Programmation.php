<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Programmation
 *
 * @property string $code_ec
 * @property string $num_salle
 * @property string $code_pers
 * @property Carbon $date
 * @property Carbon $heure_debut
 * @property Carbon $heure_fin
 * @property int $nbre_heure
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Ec $ec
 * @property Salle $salle
 * @property Personnel $personnel
 *
 * @package App\Models
 */
class Programmation extends Model
{
	use HasFactory;
	protected $table = 'programmation';
	public $incrementing = false; // Composite primary key
	public $timestamps = true;
	protected $casts = [
		'date' => 'datetime',
		'heure_debut' => 'datetime',
		'heure_fin' => 'datetime',
		'nbre_heure' => 'int'
	];

	protected $fillable = [
		'code_ec',
		'num_salle',
		'code_pers',
		'date',
		'heure_debut',
		'heure_fin',
		'nbre_heure',
		'status'
	];

    protected $primaryKey = ['code_ec','num_salle','code_pers']; // Eloquent doesn't directly support composite primary keys, but this is for documentation/clarity

	public function ec(): BelongsTo
	{
		return $this->belongsTo(Ec::class, 'code_ec', 'code_ec');
	}

	public function salle(): BelongsTo
	{
		return $this->belongsTo(Salle::class, 'num_salle', 'num_sale');
	}

	public function personnel(): BelongsTo
	{
		return $this->belongsTo(Personnel::class, 'code_pers', 'code_pers');
	}
}
