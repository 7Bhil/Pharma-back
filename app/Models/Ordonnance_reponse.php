<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ordonnance_reponse extends Model
{
    use HasFactory;

    protected $table = 'ordonnance_reponses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ordonnance_id',
        'pharmacie_id',
        'decision',
        'produits_disponibles',
        'commentaire',
    ];

    /**
     * L'ordonnance concernée par cette réponse.
     */
    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class, 'ordonnance_id');
    }

    /**
     * La pharmacie ayant répondu.
     */
    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class, 'pharmacie_id');
    }
}
