<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ordonnance extends Model
{
    use HasFactory;

    protected $table = 'ordonnances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'client_name',
        'client_phone',
        'access_token',
        'file_path',
        'status',
    ];

    /**
     * Le client connecté ayant soumis l'ordonnance (null pour un visiteur).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Les réponses fournies par les différentes pharmacies.
     */
    public function ordonnanceReponses(): HasMany
    {
        return $this->hasMany(Ordonnance_reponse::class, 'ordonnance_id');
    }

    /**
     * Les commandes générées à partir de cette ordonnance.
     */
    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class, 'ordonnance_id');
    }
}
