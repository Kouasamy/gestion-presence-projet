<?php

namespace App\Repositories;

use App\Models\Matiere;
use Illuminate\Database\Eloquent\Collection;

class MatiereRepository
{
    /**
     * Récupérer toutes les matières
     */
    public function getAll(): Collection
    {
        return Matiere::all();
    }

    /**
     * Récupérer une matière par son ID
     */
    public function findById(int $id): ?Matiere
    {
        return Matiere::findOrFail($id);
    }

    /**
     * Récupérer une matière par son nom
     */
    public function findByName(string $nomMatiere): ?Matiere
    {
        return Matiere::where('nom_matiere', $nomMatiere)->first();
    }

    /**
     * Créer une nouvelle matière
     */
    public function create(array $data): Matiere
    {
        return Matiere::create($data);
    }

    /**
     * Mettre à jour une matière
     */
    public function update(Matiere $matiere, array $data): Matiere
    {
        $matiere->update($data);

        return $matiere;
    }

    /**
     * Supprimer une matière
     */
    public function delete(Matiere $matiere): ?bool
    {
        return $matiere->delete();
    }

    /**
     * Vérifier si une matière est utilisée dans des séances
     */
    public function isUsedInSeances(int $matiereId): bool
    {
        return Matiere::findOrFail($matiereId)->seances()->exists();
    }

    /**
     * Récupérer les matières avec le nombre de séances associées
     */
    public function getWithSeanceCount(): Collection
    {
        return Matiere::withCount('seances')->get();
    }
}
