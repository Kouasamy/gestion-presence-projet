<?php

namespace App\Services;

use App\Models\Semestre;
use Illuminate\Database\Eloquent\Collection;

class SemestreService
{
    /**
     * Récupérer tous les semestres
     */
    public function getAllSemestres(): Collection
    {
        return Semestre::with('anneeAcademique')->get();
    }

    /**
     * Récupérer un semestre par son ID
     */
    public function getSemestreById(int $id): Semestre
    {
        return Semestre::findOrFail($id);
    }

    /**
     * Créer un nouveau semestre
     */
    public function createSemestre(array $data): Semestre
    {
        return Semestre::create([
            'nom' => $data['nom'],
            'date_debut_semestre' => $data['date_debut_semestre'],
            'date_fin_semestre' => $data['date_fin_semestre'],
            'annees_academiques_id' => $data['annees_academiques_id'],
        ]);
    }

    /**
     * Mettre à jour un semestre existant
     */
    public function updateSemestre(Semestre $semestre, array $data): Semestre
    {
        $semestre->update([
            'nom' => $data['nom'],
            'date_debut_semestre' => $data['date_debut_semestre'],
            'date_fin_semestre' => $data['date_fin_semestre'],
            'annees_academiques_id' => $data['annees_academiques_id'],
        ]);

        return $semestre;
    }

    /**
     * Supprimer un semestre
     */
    public function deleteSemestre(Semestre $semestre): ?bool
    {
        return $semestre->delete();
    }

    /**
     * Récupérer les semestres pour une année académique
     */
    public function getSemestresForAnnee(int $anneeId): Collection
    {
        return Semestre::where('annees_academiques_id', $anneeId)
            ->orderBy('date_debut_semestre')
            ->get();
    }

    /**
     * Récupérer le semestre en cours
     */
    public function getCurrentSemestre(): ?Semestre
    {
        $now = now();

        return Semestre::where('date_debut_semestre', '<=', $now)
            ->where('date_fin_semestre', '>=', $now)
            ->first();
    }
}
