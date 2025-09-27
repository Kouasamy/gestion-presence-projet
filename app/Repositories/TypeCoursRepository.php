<?php

namespace App\Repositories;

use App\Models\TypeCours;
use Illuminate\Database\Eloquent\Collection;

class TypeCoursRepository
{
    /**
     * Récupérer tous les types de cours
     */
    public function getAll(): Collection
    {
        return TypeCours::all();
    }

    /**
     * Récupérer un type de cours par son ID
     */
    public function findById(int $id): ?TypeCours
    {
        return TypeCours::findOrFail($id);
    }

    /**
     * Récupérer un type de cours par son nom
     */
    public function findByName(string $nomTypeCours): ?TypeCours
    {
        return TypeCours::where('nom_type_cours', $nomTypeCours)->first();
    }

    /**
     * Créer un nouveau type de cours
     */
    public function create(array $data): TypeCours
    {
        return TypeCours::create($data);
    }

    /**
     * Mettre à jour un type de cours
     */
    public function update(TypeCours $typeCours, array $data): TypeCours
    {
        $typeCours->update($data);

        return $typeCours;
    }

    /**
     * Supprimer un type de cours
     */
    public function delete(TypeCours $typeCours): ?bool
    {
        return $typeCours->delete();
    }

    /**
     * Vérifier si un type de cours est utilisé dans des séances
     */
    public function isUsedInSeances(int $typeCoursId): bool
    {
        return TypeCours::findOrFail($typeCoursId)->seances()->exists();
    }

    /**
     * Récupérer les types de cours avec le nombre de séances associées
     */
    public function getWithSeanceCount(): Collection
    {
        return TypeCours::withCount('seances')->get();
    }
}
