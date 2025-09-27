<?php

namespace App\Repositories;

use App\Models\JustificationAbsence;
use App\Models\Presence;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PresenceRepository
{
    /**
     * Récupérer toutes les présences pour une séance
     */
    public function getAllForSeance(int $seanceId): Collection
    {
        return Presence::where('seance_id', $seanceId)
            ->get()
            ->keyBy('etudiant_id');
    }

    /**
     * Récupérer toutes les présences pour un étudiant
     */
    public function getAllForEtudiant(int $etudiantId): Collection
    {
        return Presence::where('etudiant_id', $etudiantId)
            ->with(['seance.matiere', 'statutPresence'])
            ->get();
    }

    /**
     * Récupérer toutes les absences pour un étudiant
     */
    public function getAbsencesForEtudiant(int $etudiantId): Collection
    {
        return Presence::where('etudiant_id', $etudiantId)
            ->with('seance.matiere', 'justificationAbsence')
            ->whereHas('statutPresence', fn ($q) => $q->where('nom_statut_presence', 'absent'))
            ->get();
    }

    /**
     * Récupérer toutes les absences pour un coordinateur
     */
    public function getAbsencesForCoordinateur(array $filters = []): LengthAwarePaginator
    {
        $query = Presence::whereHas('seance', function ($q) {
            $q->where('coordinateur_id', Auth::user()->coordinateur->id);
        })
            ->where('statut_presence_id', 2) // 2 = Absent
            ->with(['seance.classe', 'seance.matiere', 'etudiant.user', 'justificationAbsence']);

        // Filtre par classe
        if (isset($filters['classe']) && ! empty($filters['classe'])) {
            $query->whereHas('seance', function ($q) use ($filters) {
                $q->where('classe_id', $filters['classe']);
            });
        }

        // Filtre par statut de justification
        if (isset($filters['status']) && ! empty($filters['status'])) {
            if ($filters['status'] === 'justifiee') {
                $query->whereHas('justificationAbsence');
            } elseif ($filters['status'] === 'non_justifiee') {
                $query->whereDoesntHave('justificationAbsence');
            }
        }

        // Filtre par date
        if (isset($filters['date_debut']) && ! empty($filters['date_debut'])) {
            $query->whereHas('seance', function ($q) use ($filters) {
                $q->whereDate('date_seance', '>=', $filters['date_debut']);
            });
        }
        if (isset($filters['date_fin']) && ! empty($filters['date_fin'])) {
            $query->whereHas('seance', function ($q) use ($filters) {
                $q->whereDate('date_seance', '<=', $filters['date_fin']);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Récupérer une présence par son ID
     */
    public function findById(int $id): ?Presence
    {
        return Presence::findOrFail($id);
    }

    /**
     * Récupérer une présence avec ses relations
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Presence
    {
        return Presence::with($relations)->findOrFail($id);
    }

    /**
     * Créer ou mettre à jour une présence
     */
    public function updateOrCreate(array $conditions, array $values): Presence
    {
        return Presence::updateOrCreate($conditions, $values);
    }

    /**
     * Créer une justification d'absence
     */
    public function createJustification(array $data): JustificationAbsence
    {
        return JustificationAbsence::create($data);
    }

    /**
     * Vérifier si une présence a une justification
     */
    public function hasJustification(int $presenceId): bool
    {
        return JustificationAbsence::where('presence_id', $presenceId)->exists();
    }

    /**
     * Récupérer les statistiques de présence pour un étudiant
     */
    public function getStatistiquesForEtudiant(int $etudiantId): array
    {
        $presences = Presence::where('etudiant_id', $etudiantId)
            ->with('seance.matiere', 'statutPresence')
            ->get();

        $total = $presences->count();
        $presents = $presences->filter(function ($presence) {
            return $presence->statutPresence->nom_statut_presence === 'présent';
        })->count();

        $tauxGlobal = $total > 0 ? round($presents / $total * 100, 1) : 0;

        $absencesNonJustifiees = $presences->filter(function ($presence) {
            return $presence->statutPresence->nom_statut_presence === 'absent' && ! $presence->justificationAbsence;
        })->count();

        $presencesParMatiere = $presences->groupBy(function ($presence) {
            return $presence->seance->matiere->nom_matiere;
        })->map(function ($items, $matiere) {
            $total = $items->count();
            $presents = $items->filter(function ($presence) {
                return $presence->statutPresence->nom_statut_presence === 'présent';
            })->count();

            $taux = $total > 0 ? round($presents / $total * 100, 1) : 0;

            return [
                'matiere' => $matiere,
                'taux' => $taux,
                'couleur' => $this->getCouleurTaux($taux),
            ];
        })->values();

        return [
            'tauxGlobal' => $tauxGlobal,
            'absencesNonJustifiees' => $absencesNonJustifiees,
            'presencesParMatiere' => $presencesParMatiere,
        ];
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
