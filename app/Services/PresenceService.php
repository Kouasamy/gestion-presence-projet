<?php

namespace App\Services;

use App\Models\JustificationAbsence;
use App\Models\Presence;
use App\Models\Seance;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PresenceService
{
    /**
     * Enregistrer les présences pour une séance
     */
    public function storePresences(int $seanceId, ?int $coordinateurId = null, array $presencesData = []): Collection
    {
        $presencesCollection = collect();

        foreach ($presencesData as $etudiantId => $data) {
            // On met à jour ou crée la présence (sans justification)
            $updateData = [
                'statut_presence_id' => $data['statut_presence_id'],
            ];

            if ($coordinateurId) {
                $updateData['coordinateur_id'] = $coordinateurId;
            }

            $presence = Presence::updateOrCreate(
                [
                    'seance_id' => $seanceId,
                    'etudiant_id' => $etudiantId,
                ],
                $updateData
            );

            // Si une justification est fournie, on la crée dans la table justification_absences
            if (! empty($data['justification'])) {
                // On évite de dupliquer une justification si elle existe déjà
                if (! $presence->justificationAbsence) {
                    JustificationAbsence::create([
                        'presence_id' => $presence->id,
                        'motif' => $data['justification'],
                        'date_justification' => now(),
                    ]);
                }
            }

            $presencesCollection->push($presence);
        }

        return $presencesCollection;
    }

    /**
     * Enregistrer les présences pour une séance par un coordinateur
     */
    public function storePresencesByCoordinateur(int $seanceId, int $coordinateurId, array $presencesData): Collection
    {
        $seance = Seance::findOrFail($seanceId);
        $presencesCollection = collect();

        foreach ($presencesData as $etudiantId => $data) {
            $presence = Presence::updateOrCreate(
                [
                    'seance_id' => $seanceId,
                    'etudiant_id' => $etudiantId,
                ],
                [
                    'statut_presence_id' => $data['statut_presence_id'],
                    'coordinateur_id' => $coordinateurId,
                ]
            );

            $presencesCollection->push($presence);
        }

        return $presencesCollection;
    }

    /**
     * Créer une justification d'absence
     */
    public function createJustification(int $presenceId, array $data): JustificationAbsence
    {
        $presence = Presence::findOrFail($presenceId);

        if ($presence->justificationAbsence) {
            throw new \Exception('Cette absence a déjà été justifiée.');
        }

        $justification = new JustificationAbsence([
            'presence_id' => $presence->id,
            'motif' => $data['motif'],
            'date_justification' => now(),
        ]);

        if (isset($data['document']) && $data['document']) {
            $path = $data['document']->store('justifications', 'public');
            $justification->document_path = $path;
        }

        $justification->save();

        return $justification;
    }

    /**
     * Enregistrer une justification pour une absence
     */
    public function storeJustification(int $presenceId, array $data): JustificationAbsence
    {
        $presence = Presence::findOrFail($presenceId);

        // Vérifier que l'absence n'est pas déjà justifiée
        if ($presence->justificationAbsence) {
            throw new \Exception('Cette absence a déjà été justifiée.');
        }

        $justification = new JustificationAbsence([
            'presence_id' => $presence->id,
            'motif' => $data['motif'],
            'date_justification' => $data['date_justification'] ?? now(),
        ]);

        // Gérer le document si fourni
        if (isset($data['document']) && $data['document']) {
            $path = $data['document']->store('justifications', 'public');
            $justification->document_path = $path;
        }

        $justification->save();

        return $justification;
    }

    /**
     * Justifier une absence
     */
    public function justifyAbsence(int $absenceId, array $data): JustificationAbsence
    {
        $absence = Presence::findOrFail($absenceId);

        // Vérifier que l'absence appartient à une séance gérée par ce coordinateur
        if ($absence->seance->coordinateur_id !== Auth::user()->coordinateur->id) {
            throw new \Exception('Non autorisé');
        }

        // Vérifier que l'absence n'est pas déjà justifiée
        if ($absence->justificationAbsence) {
            throw new \Exception('Cette absence a déjà été justifiée');
        }

        // Créer la justification
        $justification = JustificationAbsence::create([
            'presence_id' => $absence->id,
            'motif' => $data['motif'],
            'date_justification' => $data['date_justification'] ?? now(),
        ]);

        // Gérer le document si fourni
        if (isset($data['document']) && $data['document']) {
            $path = $data['document']->store('justifications', 'public');
            $justification->document_path = $path;
            $justification->save();
        }

        return $justification;
    }

    /**
     * Récupérer les présences pour une séance
     */
    public function getPresencesForSeance(int $seanceId): Collection
    {
        return Presence::where('seance_id', $seanceId)
            ->get()
            ->keyBy('etudiant_id');
    }

    /**
     * Récupérer les absences pour un coordinateur
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAbsencesForCoordinateur(array $filters = [])
    {
        $query = Presence::whereHas('seance', function ($q) {
            $q->where('coordinateur_id', Auth::user()->coordinateur->id);
        })
            ->where('statut_presence_id', 2)
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
     * Récupérer les présences pour un coordinateur
     * 
     * Cette méthode récupère uniquement les séances de type Workshop et E-learning,
     * car ce sont les types de séances dont la gestion des présences est sous la
     * responsabilité du coordinateur (et non de l'enseignant).
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPresencesForCoordinateur(array $filters = [])
    {
        $query = Seance::where('coordinateur_id', Auth::user()->coordinateur->id)
            ->whereDate('date_seance', '<=', now())
            // Ajouter cette condition pour filtrer par type de cours
            ->whereHas('typeCours', function ($q) {
                $q->whereIn('nom_type_cours', ['Workshop', 'E-learning']);
            })
            ->with(['classe', 'presences.etudiant.user', 'typeCours'])
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
