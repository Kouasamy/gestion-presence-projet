<?php

namespace App\Services;

use App\Models\TypeCours;
use App\Repositories\TypeCoursRepository;

class TypeCoursService
{
    protected $typeCoursRepository;

    public function __construct(TypeCoursRepository $typeCoursRepository)
    {
        $this->typeCoursRepository = $typeCoursRepository;
    }

    /**
     * Récupérer tous les types de cours
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTypesCours()
    {
        return $this->typeCoursRepository->getAll();
    }

    /**
     * Créer un nouveau type de cours
     */
    public function createTypeCours(array $data): TypeCours
    {
        return $this->typeCoursRepository->create([
            'nom_type_cours' => $data['nom_type_cours'],
        ]);
    }

    /**
     * Mettre à jour un type de cours existant
     */
    public function updateTypeCours(TypeCours $typeCours, array $data): TypeCours
    {
        return $this->typeCoursRepository->update($typeCours, [
            'nom_type_cours' => $data['nom_type_cours'],
        ]);
    }

    /**
     * Supprimer un type de cours
     *
     * @throws \Exception Si le type de cours est utilisé dans des séances
     */
    public function deleteTypeCours(TypeCours $typeCours): ?bool
    {
        // Vérifier si le type de cours est utilisé dans des séances
        if ($this->isTypeCoursUsedInSeances($typeCours->id)) {
            throw new \Exception("Impossible de supprimer ce type de cours car il est utilisé dans des séances.");
        }

        return $this->typeCoursRepository->delete($typeCours);
    }

    /**
     * Récupérer un type de cours par son ID
     */
    public function getTypeCoursById(int $id): TypeCours
    {
        return $this->typeCoursRepository->findById($id);
    }

    /**
     * Vérifier si un type de cours est utilisé dans des séances
     */
    public function isTypeCoursUsedInSeances(int $typeCoursId): bool
    {
        return $this->typeCoursRepository->isUsedInSeances($typeCoursId);
    }
}
