<?php

namespace App\Repositories;

use App\Models\Etudiant;
use Illuminate\Database\Eloquent\Collection;

class EtudiantRepository
{
    /**
     * Récupérer tous les étudiants avec filtres
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Etudiant::with(['user', 'classes']);

        if (isset($filters['classe']) && ! empty($filters['classe'])) {
            $query->whereHas('classes', function ($q) use ($filters) {
                $q->where('classes.id', $filters['classe']);
            });
        }

        return $query->get();
    }

    /**
     * Récupérer un étudiant par son ID
     */
    public function findById(int $id): ?Etudiant
    {
        return Etudiant::findOrFail($id);
    }

    /**
     * Récupérer un étudiant avec ses relations
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Etudiant
    {
        return Etudiant::with($relations)->findOrFail($id);
    }

    /**
     * Assigner un étudiant à une classe
     */
    public function assignerClasse(int $etudiantId, int $classeId, int $anneeAcademiqueId, string $dateDebut, ?string $dateFin = null): bool
    {
        $etudiant = Etudiant::findOrFail($etudiantId);

        // Vérifier si déjà assigné à cette classe pour cette année
        $dejaAssigne = $etudiant->classes()->wherePivot('classe_id', $classeId)
            ->wherePivot('annee_academique_id', $anneeAcademiqueId)
            ->exists();

        if ($dejaAssigne) {
            return false;
        }

        $etudiant->classes()->attach($classeId, [
            'annee_academique_id' => $anneeAcademiqueId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ]);

        return true;
    }

    /**
     * Désinscrire un étudiant d'une classe
     */
    public function desinscrireClasse(int $etudiantId, int $classeId): bool
    {
        $etudiant = Etudiant::findOrFail($etudiantId);
        $etudiant->classes()->detach($classeId);

        return true;
    }

    /**
     * Récupérer les étudiants avec un taux de présence inférieur à un seuil
     */
    public function getEtudiantsAvecTauxInferieur(float $seuil): Collection
    {
        return Etudiant::with('user')
            ->whereHas('presences')
            ->get()
            ->filter(function ($etudiant) use ($seuil) {
                $total = $etudiant->presences()->count();
                $absents = $etudiant->presences()
                    ->where('statut_presence_id', 2)
                    ->count();

                $taux = $total > 0 ? 100 - ($absents / $total * 100) : 0;

                return $taux < $seuil;
            });
    }

    /**
     * Récupérer le taux de présence pour un étudiant
     */
    public function getTauxPresence(int $etudiantId): float
    {
        $etudiant = Etudiant::findOrFail($etudiantId);
        $total = $etudiant->presences()->count();
        $presents = $etudiant->presences()
            ->whereHas('statutPresence', function ($q) {
                $q->where('nom_statut_presence', 'présent');
            })->count();

        return $total > 0 ? round($presents / $total * 100, 1) : 0;
    }

    /**
     * Récupérer le taux de présence par matière pour un étudiant
     */
    public function getTauxPresenceParMatiere(int $etudiantId): array
    {
        $etudiant = Etudiant::findOrFail($etudiantId);

        $presencesParMatiere = $etudiant->presences()
            ->with('seance.matiere', 'statutPresence')
            ->get()
            ->groupBy(fn ($p) => $p->seance->matiere->nom_matiere)
            ->map(fn ($items, $matiere) => [
                'matiere' => $matiere,
                'taux' => $items->count() > 0
                    ? round($items->where('statutPresence.nom_statut_presence', 'présent')->count() / $items->count() * 100, 1)
                    : 0,
                'couleur' => $this->getCouleurTaux(
                    round(
                        $items->where('statutPresence.nom_statut_presence', 'présent')->count() / max($items->count(), 1) * 100,
                        1
                    )
                ),
            ])->values()->toArray();

        return $presencesParMatiere;
    }

    /**
     * Récupère la couleur associée au taux de présence
     */
    private function getCouleurTaux(float $taux): string
    {
        if ($taux >= 70) {
            return '#1a5d1a'; // Vert foncé
        } elseif ($taux >= 50.1) {
            return '#4caf50'; // Vert clair
        } elseif ($taux >= 30.1) {
            return '#ff9800'; // Orange
        } else {
            return '#f44336'; // Rouge
        }
    }
}
