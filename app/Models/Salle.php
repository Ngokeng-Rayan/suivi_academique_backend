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
 * Class Salle
 *
 * @property string $num_sale
 * @property int $contenance
 * @property string $statut
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Programmation> $programmations
 *
 * @package App\Models
 */
class Salle extends Model
{
	use HasFactory;
	protected $table = 'salle';
	protected $primaryKey = 'num_sale';
	public $incrementing = false;
	public $timestamps = true;
	protected $casts = [
		'contenance' => 'int'
	];

	protected $fillable = [
		'num_sale',
		'contenance',
		'statut'
	];

	public function programmations(): HasMany
	{
		return $this->hasMany(Programmation::class, 'num_salle', 'num_sale');
	}
}
