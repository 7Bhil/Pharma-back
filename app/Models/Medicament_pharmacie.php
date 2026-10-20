<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medicament_pharmacie extends Model
{
    use HasFactory;

    protected $table = 'medicament_pharmacies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pharmacie_id',
        'medicament_id',
        'quantite',
        'est_disponible',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantite' => 'integer',
        ];
    }

    /**
     * La pharmacie détenant le stock.
     */
    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class, 'pharmacie_id');
    }

    /**
     * Le médicament associé.
     */
    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class, 'medicament_id');
    }
}
