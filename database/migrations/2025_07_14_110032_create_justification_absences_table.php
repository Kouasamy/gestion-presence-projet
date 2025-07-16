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
        Schema::create('justification_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presence_id')->constrained()->onDelete('cascade');
            $table->string('motif');
            $table->date('date_justification');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('justification_absences');
    }
};
