<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Ec
 *
 * @property int $code_ec
 * @property string $label_ec
 * @property string $desc_ec
 * @property string $code_ue
 * @property string|null $support_cours
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Ue $ue
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Programmation> $programmations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Enseigne> $enseignes
 *
 * @package App\Models
 */
class Ec extends Model
{
	use HasFactory;
	protected $table = 'ec';
	protected $primaryKey = 'code_ec';
	public $timestamps = true;
	protected $casts = [
		'code_ec' => 'int'
	];

	protected $appends = ['support_cours_url'];

	protected $fillable = [
		'code_ec',
		'label_ec',
		'desc_ec',
		'code_ue',
		'support_cours'
	];

	public function ue(): BelongsTo
	{
		return $this->belongsTo(Ue::class, 'code_ue', 'code_ue');
	}

	public function programmations(): HasMany
	{
		return $this->hasMany(Programmation::class, 'code_ec', 'code_ec');
	}

	public function enseignes(): HasMany
	{
		return $this->hasMany(Enseigne::class, 'code_ec', 'code_ec');
	}

	/**
	 * Accesseur pour récupérer l'URL du fichier support_cours
	 */
	public function getSupportCoursUrlAttribute()
	{
		if ($this->support_cours) {
			return asset('storage/supports-cours/' . $this->support_cours);
		}
		return null;
	}
}
