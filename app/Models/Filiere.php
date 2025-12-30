<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Filiere
 *
 * @property string $code_filiere
 * @property string $label_filiere
 * @property string $desc_filiere
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Niveau> $niveaux
 *
 * @package App\Models
 */
class Filiere extends Model
{
	use HasFactory;
	protected $table = 'filiere';
	protected $primaryKey = 'code_filiere';
	public $incrementing = false;
	public $timestamps = true;

	protected $fillable = [
		'code_filiere',
		'label_filiere',
		'desc_filiere'
	];

	public function niveaux(): HasMany
	{
		return $this->hasMany(Niveau::class, 'code_filiere', 'code_filiere');
	}
}
