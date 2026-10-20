<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacie extends Model
{
    use HasFactory;

    protected $table = 'pharmacies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'proprietaire_id',
        'nom_pharmacie',
        'ville',
        'quatier',
        'numero_pharmacie',
        'latitude',
        'longitude',
        'is_garde',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_garde' => 'boolean',
        ];
    }

    /**
     * Le propriétaire (pharmacien) de la pharmacie.
     */
    public function proprietaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    /**
     * Les médicaments disponibles dans cette pharmacie (via table pivot).
     */
    public function medicaments(): BelongsToMany
    {
        return $this->belongsToMany(Medicament::class, 'medicament_pharmacies')
            ->withPivot(['quantite', 'est_disponible'])
            ->withTimestamps();
    }

    /**
     * Les stocks détaillés des médicaments.
     */
    public function medicamentPharmacies(): HasMany
    {
        return $this->hasMany(Medicament_pharmacie::class, 'pharmacie_id');
    }

    /**
     * Les réponses fournies par cette pharmacie aux ordonnances déposées.
     */
    public function ordonnanceReponses(): HasMany
    {
        return $this->hasMany(Ordonnance_reponse::class, 'pharmacie_id');
    }

    /**
     * Les commandes reçues par cette pharmacie.
     */
    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class, 'pharmacie_id');
    }
}
