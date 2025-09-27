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
        Schema::create('historique_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seance_id')->constrained('seances')->onDelete('cascade');
            $table->foreignId('coordinateur_id')->constrained('coordinateurs')->onDelete('cascade');
            $table->date('date_initiale');
            $table->dateTime('heure_debut_initiale');
            $table->dateTime('heure_fin_initiale');
            $table->text('motif')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_reports');
    }
};
