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
        Schema::create('pharmacies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proprietaire_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nom_pharmacie');
            $table->string('ville');
            $table->string('quatier');
            $table->string('numero_pharmacie')->unique();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->boolean('is_garde')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacies');
    }
};
