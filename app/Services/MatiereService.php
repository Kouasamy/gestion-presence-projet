<?php

namespace App\Services;

use App\Models\Matiere;
use App\Repositories\MatiereRepository;

class MatiereService
{
    protected $matiereRepository;

    public function __construct(MatiereRepository $matiereRepository)
    {
        $this->matiereRepository = $matiereRepository;
    }

    /**
     * Récupérer toutes les matières
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllMatieres()
    {
        return $this->matiereRepository->getAll();
    }

    /**
     * Créer une nouvelle matière
     */
    public function createMatiere(array $data): Matiere
    {
        return $this->matiereRepository->create([
            'nom_matiere' => $data['nom_matiere'],
        ]);
    }

    /**
     * Mettre à jour une matière existante
     */
    public function updateMatiere(Matiere $matiere, array $data): Matiere
    {
        return $this->matiereRepository->update($matiere, [
            'nom_matiere' => $data['nom_matiere'],
        ]);
    }

    /**
     * Supprimer une matière
     */
    public function deleteMatiere(Matiere $matiere): ?bool
    {
        return $this->matiereRepository->delete($matiere);
    }

    /**
     * Récupérer une matière par son ID
     */
    public function getMatiereById(int $id): Matiere
    {
        return $this->matiereRepository->findById($id);
    }

    /**
     * Vérifier si une matière est utilisée dans des séances
     */
    public function isMatiereUsedInSeances(int $matiereId): bool
    {
        return $this->matiereRepository->isUsedInSeances($matiereId);
    }
}
