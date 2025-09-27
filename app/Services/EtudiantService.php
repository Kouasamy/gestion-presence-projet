<?php

namespace App\Services;

use App\Models\Classe;
use App\Models\Etudiant;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EtudiantService
{
    /**
     * Récupérer la liste des étudiants avec filtres
     */
    public function getEtudiants(array $filters = []): Collection
    {
        $query = Etudiant::with(['user', 'classes']);

        if (isset($filters['classe']) && ! empty($filters['classe'])) {
            $query->whereHas('classes', function ($q) use ($filters) {
                $q->where('classes.id', $filters['classe']);
            });
        }

        $etudiants = $query->get();

        // Calcul du taux de présence pour chaque étudiant (optionnel)
        foreach ($etudiants as $etudiant) {
            $total = $etudiant->presences()->count();
            $present = $etudiant->presences()
                ->whereHas('statutPresence', function ($q) {
                    $q->where('nom_statut_presence', 'présent');
                })->count();
            $etudiant->taux_presence = $total > 0 ? round($present / $total * 100, 1) : null;
        }

        return $etudiants;
    }

    /**
     * Récupérer tous les étudiants
     */
    public function getAllEtudiants(): Collection
    {
        return Etudiant::with(['user', 'classes.anneeAcademique'])->get();
    }

    /**
     * Récupérer les étudiants pour une classe spécifique
     */
    public function getEtudiantsForClasse(int $classeId): Collection
    {
        return Etudiant::whereHas('classes', function ($query) use ($classeId) {
            $query->where('classes.id', $classeId);
        })->with('user')->get();
    }

    /**
     * Assigner un étudiant à une classe
     */
    public function assignerClasse(int $etudiantId, int $classeId, int $anneeId, ?string $dateDebut = null): bool
    {
        $etudiant = Etudiant::findOrFail($etudiantId);

        // Vérifier si déjà assigné à cette classe pour cette année
        $dejaAssigne = $etudiant->classes()->wherePivot('classe_id', $classeId)
            ->wherePivot('annee_academique_id', $anneeId)
            ->exists();

        if ($dejaAssigne) {
            throw new \Exception('Cet étudiant est déjà assigné à cette classe pour cette année.');
        }

        $etudiant->classes()->attach($classeId, [
            'annee_academique_id' => $anneeId,
            'date_debut' => $dateDebut ?? now(),
            'date_fin' => null,
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
     * Assigner un parent à un étudiant
     */
    public function assignerParent(int $etudiantId, int $parentId): bool
    {
        $etudiant = Etudiant::findOrFail($etudiantId);

        // Vérifier si le parent est déjà assigné à cet étudiant
        if ($etudiant->parents()->where('parents.id', $parentId)->exists()) {
            return false;
        }

        // Assigner le parent à l'étudiant
        $etudiant->parents()->attach($parentId);

        return true;
    }

    /**
     * Récupérer les statistiques pour un étudiant
     */
    public function getStatistiquesEtudiant(int $etudiantId): array
    {
        $etudiant = Etudiant::findOrFail($etudiantId);

        $total = $etudiant->presences()->count();
        $presents = $etudiant->presences()->whereHas('statutPresence', fn ($q) => $q->where('nom_statut_presence', 'présent'))->count();
        $tauxGlobal = $total > 0 ? round($presents / $total * 100, 1) : 0;

        $absencesNonJustifiees = $etudiant->presences()
            ->whereHas('statutPresence', fn ($q) => $q->where('nom_statut_presence', 'absent'))
            ->whereDoesntHave('justificationAbsence')
            ->count();

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
            ])->values();

        $assiduite = $presencesParMatiere->map(fn ($item) => [
            'matiere' => $item['matiere'],
            'note' => round(($item['taux'] / 100) * 20, 1),
        ]);

        // Calculate dropped subjects notifications
        $droppedSubjects = $presencesParMatiere->filter(fn ($item) => $item['taux'] < 30)->map(fn ($item) => [
            'matiere' => $item['matiere'],
            'taux' => $item['taux'],
            'message' => "Vous êtes droppé de la matière {$item['matiere']} avec un taux de présence de {$item['taux']}%",
        ]);

        return [
            'tauxGlobal' => $tauxGlobal,
            'absencesNonJustifiees' => $absencesNonJustifiees,
            'presencesParMatiere' => $presencesParMatiere,
            'assiduite' => $assiduite,
            'droppedSubjects' => $droppedSubjects,
        ];
    }

    /**
     * Récupérer l'emploi du temps d'un étudiant
     */
    public function getEmploiDuTemps(int $etudiantId): array
    {
        $etudiant = Etudiant::findOrFail($etudiantId);
        $classe = $etudiant->classes()->latest()->first(); // Get student's current class

        if (! $classe) {
            return [
                'emploiDuTemps' => [],
                'jours' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'],
                'classe' => null,
                'error' => 'Aucune classe assignée',
            ];
        }

        // Récupérer la semaine en cours
        $maintenant = Carbon::now();
        $debutSemaine = $maintenant->copy()->startOfWeek()->format('Y-m-d');
        $finSemaine = $maintenant->copy()->endOfWeek()->format('Y-m-d');

        // Récupérer les séances de la semaine en cours pour la classe de l'étudiant
        $seances = $classe->seances()
            ->with(['matiere', 'typeCours', 'enseignant.user', 'statutSeance'])
            ->whereBetween('date_seance', [$debutSemaine, $finSemaine])
            ->orderBy('date_seance')
            ->orderBy('heure_debut')
            ->get();

        $emploiDuTemps = [];
        foreach ($seances as $seance) {
            $date = Carbon::parse($seance->date_seance);
            $jour = ucfirst($date->locale('fr')->dayName);
            $heureDebut = Carbon::parse($seance->heure_debut);
            $periode = ($heureDebut->hour < 12) ? 'matin' : 'soir';

            $emploiDuTemps[$jour][$periode] = [
                'enseignant' => $seance->enseignant->user->nom,
                'cours' => $seance->matiere->nom_matiere,
                'type' => $seance->typeCours->nom_type_cours,
                'heure_debut' => $seance->heure_debut,
                'heure_fin' => $seance->heure_fin,
                'statut' => $seance->statutSeance->nom_statut_seance ?? 'planifiée',
                'statut_id' => $seance->statut_seance_id,
                'date' => $seance->date_seance,
            ];
        }

        return [
            'emploiDuTemps' => $emploiDuTemps,
            'jours' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'],
            'classe' => $classe,
        ];
    }

    /**
     * Récupérer les absences d'un étudiant
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAbsences(int $etudiantId)
    {
        $etudiant = Etudiant::findOrFail($etudiantId);

        return $etudiant->presences()
            ->with('seance.matiere', 'justificationAbsence')
            ->whereHas('statutPresence', fn ($q) => $q->where('nom_statut_presence', 'absent'))
            ->get();
    }

    /**
     * Récupérer la note d'assiduité d'un étudiant
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNoteAssiduite(int $etudiantId)
    {
        $etudiant = Etudiant::findOrFail($etudiantId);

        return $etudiant->presences()
            ->with('seance.matiere', 'statutPresence')
            ->get()
            ->groupBy(fn ($p) => $p->seance->matiere->nom_matiere)
            ->map(fn ($items, $matiere) => [
                'matiere' => $matiere,
                'note' => round(($items->where('statutPresence.nom_statut_presence', 'présent')->count() / max($items->count(), 1)) * 20, 1),
            ])->values();
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
