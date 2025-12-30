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
 * Class Enseigne
 *
 * @property string $code_pers
 * @property int $code_ec
 * @property int|null $nbh_heure
 * @property Carbon|null $heure_debut
 * @property Carbon|null $heure_fin
 * @property string|null $statut
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Personnel $personnel
 * @property Ec $ec
 *
 * @package App\Models
 */
class Enseigne extends Model
{
	use HasFactory;
	protected $table = 'enseigne';
	public $incrementing = false; // No single primary key
	public $timestamps = true;
	protected $casts = [
		'code_ec' => 'int',
		'nbh_heure' => 'int',
		'heure_debut' => 'date',
		'heure_fin' => 'date'
	];

	protected $fillable = [
		'code_pers',
		'code_ec',
		'nbh_heure',
		'heure_debut',
		'heure_fin',
		'statut'
	];

	public function personnel(): BelongsTo
	{
		return $this->belongsTo(Personnel::class, 'code_pers', 'code_pers');
	}

	public function ec(): BelongsTo
	{
		return $this->belongsTo(Ec::class, 'code_ec', 'code_ec');
	}
}
