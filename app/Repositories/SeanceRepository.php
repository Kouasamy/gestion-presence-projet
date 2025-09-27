<?php

namespace App\Repositories;

use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SeanceRepository
{
    /**
     * Récupérer toutes les séances pour un coordinateur
     */
    public function getAllForCoordinateur(array $filters = []): Collection
    {
        $query = Seance::where('coordinateur_id', Auth::user()->coordinateur->id)
            ->with(['classe', 'matiere', 'enseignant.user', 'typeCours', 'statutSeance']);

        // Filtre par type (attendance ou justification)
        if (isset($filters['filter'])) {
            if ($filters['filter'] === 'attendance') {
                $query->whereHas('presences', function ($q) {
                    $q->whereDoesntHave('justificationAbsence');
                });
            } elseif ($filters['filter'] === 'justification') {
                $query->whereHas('presences', function ($q) {
                    $q->whereHas('justificationAbsence');
                });
            }
        }

        // Filtre par classe
        if (isset($filters['classe']) && ! empty($filters['classe'])) {
            $query->where('classe_id', $filters['classe']);
        }

        // Filtre par date
        if (isset($filters['date']) && ! empty($filters['date'])) {
            $query->whereDate('date_seance', $filters['date']);
        }

        // Filtre par type de cours
        if (isset($filters['type']) && ! empty($filters['type'])) {
            $query->where('type_cours_id', $filters['type']);
        }

        return $query->orderBy('date_seance', 'desc')
            ->orderBy('heure_debut', 'asc')
            ->get();
    }

    /**
     * Récupérer toutes les séances pour un enseignant
     */
    public function getAllForEnseignant(int $enseignantId, array $filters = []): LengthAwarePaginator
    {
        $query = Seance::where('enseignant_id', $enseignantId)
            ->with(['classe', 'matiere', 'typeCours', 'statutSeance', 'presences'])
            ->orderBy('date_seance', 'desc')
            ->orderBy('heure_debut', 'asc');

        if (isset($filters['filtre'])) {
            if ($filters['filtre'] === 'avenir') {
                $query->where(function($q) {
                    $now = Carbon::now();
                    $q->whereDate('date_seance', '>', $now->format('Y-m-d'))
                      ->orWhere(function($q2) use ($now) {
                          $q2->whereDate('date_seance', '=', $now->format('Y-m-d'))
                             ->whereTime('heure_debut', '>=', $now->format('H:i:s'));
                      });
                });
            } elseif ($filters['filtre'] === 'passe') {
                $query->where(function($q) {
                    $now = Carbon::now();
                    $q->whereDate('date_seance', '<', $now->format('Y-m-d'))
                      ->orWhere(function($q2) use ($now) {
                          $q2->whereDate('date_seance', '=', $now->format('Y-m-d'))
                             ->whereTime('heure_fin', '<', $now->format('H:i:s'));
                      });
                });
            }
        }

        return $query->paginate(10);
    }

    /**
     * Récupérer toutes les séances pour un étudiant
     */
    public function getAllForEtudiant(int $etudiantId): Collection
    {
        return Seance::whereHas('classe.etudiants', function ($query) use ($etudiantId) {
            $query->where('etudiants.id', $etudiantId);
        })
            ->with(['matiere', 'typeCours', 'enseignant.user', 'statutSeance'])
            ->orderBy('date_seance')
            ->orderBy('heure_debut')
            ->get();
    }

    /**
     * Récupérer une séance par son ID
     */
    public function findById(int $id): ?Seance
    {
        return Seance::findOrFail($id);
    }

    /**
     * Récupérer une séance avec ses relations
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Seance
    {
        return Seance::with($relations)->findOrFail($id);
    }

    /**
     * Créer une nouvelle séance
     */
    public function create(array $data): Seance
    {
        return Seance::create($data);
    }

    /**
     * Mettre à jour une séance
     */
    public function update(Seance $seance, array $data): Seance
    {
        $seance->update($data);

        return $seance;
    }

    /**
     * Supprimer une séance
     */
    public function delete(Seance $seance): ?bool
    {
        return $seance->delete();
    }

    /**
     * Vérifier les conflits d'horaire pour une classe
     */
    public function hasClasseScheduleConflict(int $classeId, string $dateSeance, string $heureDebut, string $heureFin, ?int $excludeSeanceId = null): bool
    {
        $query = Seance::where('classe_id', $classeId)
            ->where('date_seance', $dateSeance)
            ->whereNotIn('statut_seance_id', [3, 4]) // Exclure les séances annulées (3) et reportées (4)
            ->where(function ($query) use ($heureDebut, $heureFin) {
                $query->whereBetween('heure_debut', [$heureDebut, $heureFin])
                    ->orWhereBetween('heure_fin', [$heureDebut, $heureFin])
                    ->orWhere(function ($q) use ($heureDebut, $heureFin) {
                        $q->where('heure_debut', '<=', $heureDebut)
                            ->where('heure_fin', '>=', $heureFin);
                    });
            });

        if ($excludeSeanceId) {
            $query->where('id', '!=', $excludeSeanceId);
        }

        return $query->exists();
    }

    /**
     * Vérifier les conflits d'horaire pour un enseignant
     */
    public function hasEnseignantScheduleConflict(int $enseignantId, string $dateSeance, string $heureDebut, string $heureFin, ?int $excludeSeanceId = null): bool
    {
        $query = Seance::where('enseignant_id', $enseignantId)
            ->where('date_seance', $dateSeance)
            ->whereNotIn('statut_seance_id', [3, 4]) // Exclure les séances annulées (3) et reportées (4)
            ->where(function ($query) use ($heureDebut, $heureFin) {
                $query->whereBetween('heure_debut', [$heureDebut, $heureFin])
                    ->orWhereBetween('heure_fin', [$heureDebut, $heureFin])
                    ->orWhere(function ($q) use ($heureDebut, $heureFin) {
                        $q->where('heure_debut', '<=', $heureDebut)
                            ->where('heure_fin', '>=', $heureFin);
                    });
            });

        if ($excludeSeanceId) {
            $query->where('id', '!=', $excludeSeanceId);
        }

        return $query->exists();
    }

    /**
     * Récupérer les séances pour un emploi du temps
     */
    public function getForEmploiDuTemps(int $classeId, ?string $dateDebut = null, ?string $dateFin = null): Collection
    {
        $query = Seance::where('classe_id', $classeId)
            ->with(['matiere', 'enseignant.user', 'typeCours'])
            ->orderBy('date_seance')
            ->orderBy('heure_debut');

        if ($dateDebut) {
            $query->whereDate('date_seance', '>=', $dateDebut);
        }

        if ($dateFin) {
            $query->whereDate('date_seance', '<=', $dateFin);
        }

        return $query->get();
    }

    /**
     * Récupérer les séances pour les présences
     */
    public function getForPresences(array $filters = []): LengthAwarePaginator
    {
        $query = Seance::where('coordinateur_id', Auth::user()->coordinateur->id)
            ->whereDate('date_seance', '<=', now())
            ->with(['classe', 'presences.etudiant.user'])
            ->orderBy('date_seance', 'desc');

        // Filtre par classe
        if (isset($filters['classe']) && ! empty($filters['classe'])) {
            $query->where('classe_id', $filters['classe']);
        }

        // Filtre par date
        if (isset($filters['date']) && ! empty($filters['date'])) {
            $query->whereDate('date_seance', $filters['date']);
        }

        return $query->paginate(15);
    }
}
