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
 * Class Niveau
 *
 * @property int $code_niveau
 * @property string $label_niveau
 * @property string $desc_niveau
 * @property string $code_filiere
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Filiere $filiere
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Ue> $ues
 *
 * @package App\Models
 */
class Niveau extends Model
{
	use HasFactory;
	protected $table = 'niveau';
	protected $primaryKey = 'code_niveau';
	public $timestamps = true;
	public $incrementing = true;
	protected $fillable = [
		'label_niveau',
		'desc_niveau',
		'code_filiere'
	];

	public function filiere(): BelongsTo
	{
		return $this->belongsTo(Filiere::class, 'code_filiere', 'code_filiere');
	}

	public function ues(): HasMany
	{
		return $this->hasMany(Ue::class, 'code_niveau', 'code_niveau');
	}
}
