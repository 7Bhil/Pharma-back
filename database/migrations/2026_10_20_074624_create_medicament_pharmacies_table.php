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
        Schema::create('medicament_pharmacies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacie_id')->nullable()->constrained('pharmacies')->nullOnDelete();
            $table->foreignId('medicament_id')->nullable()->constrained('medicaments')->nullOnDelete();
            $table->integer('quantite');
            $table->string('est_disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicament_pharmacies');
    }
};
