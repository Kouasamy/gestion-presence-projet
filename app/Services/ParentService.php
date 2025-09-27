<?php

namespace App\Services;

use App\Models\Parents;
use Illuminate\Support\Collection;

class ParentService
{
    /**
     * Récupérer les statistiques et notifications pour un parent
     */
    public function getStatistiquesEtNotifications(int $parentId): array
    {
        $parent = Parents::findOrFail($parentId);
        $notifications = collect();

        $etudiants = $parent->etudiants()->with(['user', 'classes', 'presences.justificationAbsence'])->get();

        $totalAbsences = $justifiees = $nonJustifiees = $tauxPresence = 0;

        foreach ($etudiants as $etudiant) {
            // Statistiques
            $presences = $etudiant->presences;
            $absences = $presences->where('statut_presence_id', 2);

            $justifiees = $absences->filter(fn ($a) => $a->justificationAbsence)->count();
            $nonJustifiees = $absences->filter(fn ($a) => ! $a->justificationAbsence)->count();
            $totalAbsences = $justifiees + $nonJustifiees;

            $totalPresences = $presences->count();
            $tauxPresence = $totalPresences > 0
                ? (100 * ($totalPresences - $totalAbsences) / $totalPresences)
                : 100;

            // Séances annulées => Notification
            $droppedSeances = $etudiant->classes()
                ->with(['seances' => function ($query) {
                    $query->where('statut_seance_id', 3);
                }, 'seances.matiere'])
                ->get()
                ->pluck('seances')
                ->flatten();

            foreach ($droppedSeances as $seance) {
                $notifications->push([
                    'nom' => '⚠️ Désinscrit de : '.$seance->matiere->nom_matiere,
                    'taux' => $tauxPresence,
                    'photo' => $etudiant->photo_path
                        ? asset('storage/'.$etudiant->photo_path)
                        : 'https://ui-avatars.com/api/?name='.urlencode($etudiant->user->nom),
                ]);
            }

            // Drop automatique si taux < 30%
            if ($tauxPresence < 30) {
                $notifications->push([
                    'nom' => '❌ Votre enfant a été droppé pour faible présence',
                    'taux' => $tauxPresence,
                    'photo' => $etudiant->photo_path
                        ? asset('storage/'.$etudiant->photo_path)
                        : 'https://ui-avatars.com/api/?name='.urlencode($etudiant->user->nom),
                ]);
            }
        }

        return [
            'notifications' => $notifications,
            'etudiants' => $etudiants,
            'totalAbsences' => $totalAbsences,
            'justifiees' => $justifiees,
            'nonJustifiees' => $nonJustifiees,
            'tauxPresence' => $tauxPresence,
        ];
    }

    /**
     * Récupérer l'emploi du temps des enfants d'un parent
     */
    public function getEmploiDuTemps(int $parentId): ?Collection
    {
        $parent = Parents::findOrFail($parentId);

        // Récupérer la semaine en cours
        $maintenant = \Carbon\Carbon::now();
        $debutSemaine = $maintenant->copy()->startOfWeek()->format('Y-m-d');
        $finSemaine = $maintenant->copy()->endOfWeek()->format('Y-m-d');

        return $parent->etudiants()->with(['classes.seances' => function ($query) use ($debutSemaine, $finSemaine) {
            $query->whereBetween('date_seance', [$debutSemaine, $finSemaine])
                  ->orderBy('date_seance')
                  ->orderBy('heure_debut');
        }, 'classes.seances.matiere', 'classes.seances.enseignant.user', 'classes.seances.typeCours', 'classes.seances.statutSeance'])->get();
    }

    /**
     * Récupérer les absences des enfants d'un parent
     */
    public function getAbsences(int $parentId): Collection
    {
        $parent = Parents::findOrFail($parentId);

        $etudiants = $parent->etudiants()->with(['presences' => function ($query) {
            $query->where('statut_presence_id', 2) // Assuming 2 is the ID for 'Absent'
                ->with(['seance.matiere', 'seance.typeCours', 'justificationAbsence']);
        }])->get();

        return $etudiants->map(function ($etudiant) {
            $absencesJustifiees = $etudiant->presences->filter(fn ($absence) => $absence->justificationAbsence !== null);
            $absencesNonJustifiees = $etudiant->presences->filter(fn ($absence) => $absence->justificationAbsence === null);

            return [
                'etudiant' => $etudiant,
                'absencesJustifiees' => $absencesJustifiees,
                'absencesNonJustifiees' => $absencesNonJustifiees,
            ];
        });
    }
}
