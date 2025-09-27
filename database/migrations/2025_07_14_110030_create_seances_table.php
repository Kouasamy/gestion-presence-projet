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
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('matiere_id');
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('enseignant_id')->nullable();
            $table->unsignedBigInteger('type_cours_id');
            $table->unsignedBigInteger('statut_seance_id');
            $table->unsignedBigInteger('coordinateur_id')->nullable();
            $table->date('date_seance');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->timestamps();

            $table->index(['classe_id', 'date_seance']);
            $table->index('enseignant_id');
            $table->index('matiere_id');
            $table->index('coordinateur_id');
        });

        // Ajouter les contraintes de clés étrangères après la création des tables
        Schema::table('seances', function (Blueprint $table) {
            $table->foreign('matiere_id')->references('id')->on('matieres')->onDelete('restrict');
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('restrict');
            $table->foreign('enseignant_id')->references('id')->on('enseignants')->onDelete('set null');
            $table->foreign('type_cours_id')->references('id')->on('type_cours')->onDelete('restrict');
            $table->foreign('statut_seance_id')->references('id')->on('statut_seances')->onDelete('restrict');
            $table->foreign('coordinateur_id')->references('id')->on('coordinateurs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seances');
    }
};
