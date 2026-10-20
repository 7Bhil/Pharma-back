<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commande extends Model
{
    use HasFactory;

    protected $table = 'commandes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'pharmacie_id',
        'ordonnance_id',
        'nom_client',
        'telephone',
        'email',
        'numero_commande',
        'montant_total',
        'statut',
        'statut_paiement',
        'mode_paiement',
        'code_retrait',
        'qr_code_path',
        'qr_code_sent',
        'qr_code_sent_at',
        'canal_envoi_qr',
        'qr_code_expires_at',
        'is_scanned',
        'scanned_at',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'montant_total' => 'decimal:2',
            'qr_code_sent' => 'boolean',
            'is_scanned' => 'boolean',
            'qr_code_sent_at' => 'datetime',
            'qr_code_expires_at' => 'datetime',
            'scanned_at' => 'datetime',
        ];
    }

    /**
     * Relation avec l'utilisateur (client connecté, optionnel).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la pharmacie préparatrice.
     */
    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class);
    }

    /**
     * Relation avec l'ordonnance associée (optionnelle).
     */
    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class);
    }

    /**
     * Vérifier si le QR code est expiré.
     */
    public function isQrCodeExpired(): bool
    {
        return $this->qr_code_expires_at !== null && $this->qr_code_expires_at->isPast();
    }

    /**
     * Vérifier si la commande est prête à être retirée via QR code.
     */
    public function isValidForPickup(): bool
    {
        return !$this->is_scanned && !$this->isQrCodeExpired() && in_array($this->statut, ['prete', 'validee']);
    }

    /**
     * Marquer le QR Code comme envoyé au client.
     */
    public function markQrCodeSent(string $canal = 'sms'): bool
    {
        return $this->update([
            'qr_code_sent' => true,
            'qr_code_sent_at' => now(),
            'canal_envoi_qr' => $canal,
        ]);
    }

    /**
     * Valider le retrait de la commande lors du scan du QR code.
     */
    public function markAsScanned(): bool
    {
        return $this->update([
            'is_scanned' => true,
            'scanned_at' => now(),
            'statut' => 'recuperee',
        ]);
    }
}
