<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécuter les migrations.
     */
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seance_id');
            $table->unsignedBigInteger('etudiant_id');
            $table->unsignedBigInteger('statut_presence_id');
            $table->unsignedBigInteger('coordinateur_id')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->index(['seance_id', 'etudiant_id']);
            $table->index('statut_presence_id');
            $table->index('coordinateur_id');
        });

        // Ajouter les contraintes de clés étrangères après la création des tables
        Schema::table('presences', function (Blueprint $table) {
            $table->foreign('seance_id')->references('id')->on('seances')->onDelete('cascade');
            $table->foreign('etudiant_id')->references('id')->on('etudiants')->onDelete('cascade');
            $table->foreign('statut_presence_id')->references('id')->on('statut_presences')->onDelete('restrict');
            $table->foreign('coordinateur_id')->references('id')->on('coordinateurs')->onDelete('set null');
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
