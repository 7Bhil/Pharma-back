<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pharmacie_id')->nullable()->constrained('pharmacies')->nullOnDelete();
            $table->foreignId('ordonnance_id')->nullable()->constrained('ordonnances')->nullOnDelete();
            
            // Informations client
            $table->string('nom_client');
            $table->string('telephone');
            $table->string('email')->nullable();
            
            // Détails de la commande
            $table->string('numero_commande')->unique();
            $table->decimal('montant_total', 10, 2)->nullable();
            $table->enum('statut', ['en_attente', 'validee', 'prete', 'recuperee', 'annulee'])->default('en_attente');
            $table->enum('statut_paiement', ['en_attente', 'paye', 'echoue'])->default('en_attente');
            $table->string('mode_paiement')->nullable(); // ex: 'especes', 'carte', 'mobile_money'
            
            // Champs pour la génération, l'envoi et la validation du QR Code
            $table->string('code_retrait', 64)->unique(); // Code secret / token unique encodé dans le QR code pour vérification au retrait
            $table->string('qr_code_path')->nullable(); // Chemin d'accès ou URL de l'image du QR Code
            $table->boolean('qr_code_sent')->default(false); // Indique si le QR code a été envoyé au client
            $table->timestamp('qr_code_sent_at')->nullable(); // Date et heure de l'envoi du QR code
            $table->enum('canal_envoi_qr', ['sms', 'whatsapp', 'email'])->nullable(); // Canal utilisé pour l'envoi
            $table->timestamp('qr_code_expires_at')->nullable(); // Date/heure d'expiration du QR code
            $table->boolean('is_scanned')->default(false); // Indique si le QR code a été scanné à la pharmacie
            $table->timestamp('scanned_at')->nullable(); // Date et heure du scan / retrait en pharmacie
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
