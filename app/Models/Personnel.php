<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


/**
 * Class Personnel
 *
 * @property string $code_pers
 * @property string $nom_pers
 * @property string|null $prenom_pers
 * @property string $sexe_pers
 * @property string $phone_pers
 * @property string $login_pers
 * @property string $pwd_pers
 * @property string $type_pers
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Programmation> $programmations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Enseigne> $enseignes
 *
 * @package App\Models
 */
class Personnel extends Authenticatable
{
	use HasFactory;
	use HasApiTokens;

	protected $table = 'personnel';
	protected $primaryKey = 'code_pers';
	public $incrementing = false;
	public $timestamps = true;

	protected $fillable = [
		'code_pers',
		'nom_pers',
		'prenom_pers',
		'sexe_pers',
		'phone_pers',
		'login_pers',
		'pwd_pers',
		'type_pers'
	];

	public function programmations(): HasMany
	{
		return $this->hasMany(Programmation::class, 'code_pers', 'code_pers');
	}

	public function enseignes(): HasMany
	{
		return $this->hasMany(Enseigne::class, 'code_pers', 'code_pers');
	}
}
