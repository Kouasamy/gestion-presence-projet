<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use Illuminate\Database\Eloquent\Collection;

class AnneeAcademiqueService
{
    /**
     * Récupérer toutes les années académiques
     */
    public function getAllAnnees(): Collection
    {
        return AnneeAcademique::withCount('semestres')->get();
    }

    /**
     * Récupérer une année académique par son ID
     */
    public function getAnneeById(int $id): AnneeAcademique
    {
        return AnneeAcademique::findOrFail($id);
    }

    /**
     * Créer une nouvelle année académique
     */
    public function createAnnee(array $data): AnneeAcademique
    {
        return AnneeAcademique::create([
            'annee' => $data['annee'],
        ]);
    }

    /**
     * Mettre à jour une année académique existante
     */
    public function updateAnnee(int $id, array $data): AnneeAcademique
    {
        $annee = AnneeAcademique::findOrFail($id);
        $annee->update([
            'annee' => $data['annee'],
        ]);

        return $annee;
    }

    /**
     * Supprimer une année académique
     */
    public function deleteAnnee(int $id): ?bool
    {
        $annee = AnneeAcademique::findOrFail($id);

        return $annee->delete();
    }

    /**
     * Récupérer l'année académique en cours
     */
    public function getCurrentAnnee(): ?AnneeAcademique
    {
        // Logique pour déterminer l'année académique en cours
        // Par exemple, l'année la plus récente
        return AnneeAcademique::orderBy('annee', 'desc')->first();
    }
}
