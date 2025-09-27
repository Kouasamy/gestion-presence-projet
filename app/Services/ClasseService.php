<?php

namespace App\Services;

use App\Models\Classe;

class ClasseService
{
    /**
     * Créer une nouvelle classe
     */
    public function createClasse(array $data): Classe
    {
        return Classe::create([
            'nom_classe' => $data['nom_classe'],
        ]);
    }

    /**
     * Mettre à jour une classe existante
     */
    public function updateClasse(Classe $classe, array $data): Classe
    {
        $classe->update([
            'nom_classe' => $data['nom_classe'],
        ]);

        return $classe;
    }

    /**
     * Supprimer une classe
     */
    public function deleteClasse(Classe $classe): ?bool
    {
        return $classe->delete();
    }

    /**
     * Récupérer toutes les classes
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllClasses()
    {
        return Classe::all();
    }

    /**
     * Récupérer une classe par son ID
     */
    public function getClasseById(int $id): Classe
    {
        return Classe::findOrFail($id);
    }

    /**
     * Vérifier si une classe a des étudiants
     */
    public function hasEtudiants(int $classeId): bool
    {
        return Classe::findOrFail($classeId)->etudiants()->exists();
    }

    /**
     * Vérifier si une classe a des séances
     */
    public function hasSeances(int $classeId): bool
    {
        return Classe::findOrFail($classeId)->seances()->exists();
    }

    /**
     * Récupérer les classes avec le nombre d'étudiants
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getClassesWithEtudiantCount()
    {
        return Classe::withCount('etudiants')->get();
    }

    /**
     * Récupérer les classes avec le nombre de séances
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getClassesWithSeanceCount()
    {
        return Classe::withCount('seances')->get();
    }

    /**
     * Récupérer les étudiants d'une classe
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEtudiantsForClasse(int $classeId)
    {
        return Classe::findOrFail($classeId)->etudiants()->with('user')->get();
    }

    /**
     * Récupérer les séances d'une classe
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSeancesForClasse(int $classeId)
    {
        return Classe::findOrFail($classeId)->seances()->with(['matiere', 'enseignant.user', 'typeCours'])->get();
    }
}
