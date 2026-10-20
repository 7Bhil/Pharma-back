<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicament extends Model
{
    use HasFactory;

    protected $table = 'medicaments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom_medicaments',
        'dci',
        'dosage',
        'categorie',
        'forme',
        'requires_prescription',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requires_prescription' => 'boolean',
        ];
    }

    /**
     * Les pharmacies détenant ce médicament (via table pivot).
     */
    public function pharmacies(): BelongsToMany
    {
        return $this->belongsToMany(Pharmacie::class, 'medicament_pharmacies')
            ->withPivot(['quantite', 'est_disponible'])
            ->withTimestamps();
    }

    /**
     * Les entrées de stock détaillées par pharmacie.
     */
    public function medicamentPharmacies(): HasMany
    {
        return $this->hasMany(Medicament_pharmacie::class, 'medicament_id');
    }
}
