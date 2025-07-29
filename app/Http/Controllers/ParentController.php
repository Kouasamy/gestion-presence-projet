<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{

    public function dashboard()
    {
        $parent = Auth::user()->parent;
        $notifications = collect();

        $etudiant = null;
        $totalAbsences = $justifiees = $nonJustifiees = $tauxPresence = 0;

        if ($parent) {
            $etudiants = $parent->etudiants()->with(['user', 'classes', 'presences.justificationAbsence'])->get();

            foreach ($etudiants as $etudiant) {
                // Statistiques
                $presences = $etudiant->presences;
                $absences = $presences->where('statut_presence_id', 2);

                $justifiees = $absences->filter(fn($a) => $a->justificationAbsence)->count();
                $nonJustifiees = $absences->filter(fn($a) => !$a->justificationAbsence)->count();
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
                        'nom' => "⚠️ Désinscrit de : " . $seance->matiere->nom_matiere,
                        'taux' => $tauxPresence,
                        'photo' => $etudiant->photo_path
                            ? asset('storage/' . $etudiant->photo_path)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($etudiant->user->nom),
                    ]);
                }

                // Drop automatique si taux < 30%
                if ($tauxPresence < 30) {
                    $notifications->push([
                        'nom' => "❌ Votre enfant a été droppé pour faible présence",
                        'taux' => $tauxPresence,
                        'photo' => $etudiant->photo_path
                            ? asset('storage/' . $etudiant->photo_path)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($etudiant->user->nom),
                    ]);
                }
            }
        }

        return view('parent.dashboardParent', compact(
            'notifications',
            'etudiants',
            'totalAbsences',
            'justifiees',
            'nonJustifiees',
            'tauxPresence'
        ));
    }


    /**
     * Display the child's schedule (read-only).
     */
    public function emploiDuTemps()
    {
        $parent = Auth::user()->parent;
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Aucun parent associé trouvé.');
        }
        $etudiants = $parent->etudiants()->with(['classes.seances' => function ($query) {
            $query->orderBy('date_seance')->orderBy('heure_debut');
        }, 'classes.seances.matiere', 'classes.seances.enseignant.user', 'classes.seances.typeCours'])->get();

        return view('parent.emploiDuTemps', compact('etudiants'));
    }

    /**
     *
     */
    public function absences()
    {
        $parent = Auth::user()->parent;
        if (!$parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Aucun parent associé trouvé.');
        }
        $etudiants = $parent->etudiants()->with(['presences' => function ($query) {
            $query->where('statut_presence_id', 2) // Assuming 2 is the ID for 'Absent'
                ->with(['seance.matiere', 'seance.typeCours', 'justificationAbsence']);
        }])->get();

        $absencesData = $etudiants->map(function ($etudiant) {
            $absencesJustifiees = $etudiant->presences->filter(fn($absence) => $absence->justificationAbsence !== null);
            $absencesNonJustifiees = $etudiant->presences->filter(fn($absence) => $absence->justificationAbsence === null);

            return [
                'etudiant' => $etudiant,
                'absencesJustifiees' => $absencesJustifiees,
                'absencesNonJustifiees' => $absencesNonJustifiees,
            ];
        });

        return view('parent.absences', compact('absencesData'));
    }
}
