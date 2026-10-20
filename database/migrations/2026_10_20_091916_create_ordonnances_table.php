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
        Schema::create('ordonnances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // null pour visiteur
            // Champs pour visiteur
            $table->string('client_name')->nullable();
            $table->string('client_phone'); // Indispensable pour la notification et le lien SMS
            // Sécurité et accès exclusif
            $table->string('access_token', 64)->unique(); // Le token secret non devinable
            $table->string('file_path');
            $table->enum('status', ['pending', 'answered', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordonnances');
    }
};
