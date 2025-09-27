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
        Schema::create('justification_absences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('presence_id');
            $table->string('motif');
            $table->date('date_justification');
            $table->string('document_path')->nullable();
            $table->unsignedBigInteger('validee_par')->nullable();
            $table->timestamps();

            $table->index('presence_id');
            $table->index('validee_par');
        });

        // Ajouter les contraintes de clés étrangères après la création des tables
        Schema::table('justification_absences', function (Blueprint $table) {
            $table->foreign('presence_id')->references('id')->on('presences')->onDelete('cascade');
            $table->foreign('validee_par')->references('id')->on('coordinateurs')->onDelete('set null');
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('justification_absences');
    }
};
