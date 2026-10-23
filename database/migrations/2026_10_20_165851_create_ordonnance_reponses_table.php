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
        Schema::create('ordonnance_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordonnance_id')->nullable()->constrained('ordonnances')->nullOnDelete();
            $table->foreignId('pharmacie_id')->nullable()->constrained('pharmacies')->nullOnDelete();
            $table->enum('decision', ['Tous_disponible', 'Partiellement', 'Aucun']);
            $table->string('produits_disponibles')->nullable();
            $table->string('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordonnance_reponses');
    }
};
