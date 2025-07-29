<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtudiantController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'isEtudiant']);
    }

    public function dashboard()
    {
        $etudiant = Auth::user()->etudiant;

        $total = $etudiant->presences()->count();
        $presents = $etudiant->presences()->whereHas('statutPresence', fn($q) => $q->where('nom_statut_presence','présent'))->count();
        $tauxGlobal = $total > 0 ? round($presents / $total * 100, 1) : 0;

        $absencesNonJustifiees = $etudiant->presences()
            ->whereHas('statutPresence', fn($q) => $q->where('nom_statut_presence','absent'))
            ->whereDoesntHave('justificationAbsence')
            ->count();

        $presencesParMatiere = $etudiant->presences()
            ->with('seance.matiere', 'statutPresence')
            ->get()
            ->groupBy(fn($p) => $p->seance->matiere->nom_matiere)
            ->map(fn($items, $matiere) => [
                'matiere' => $matiere,
                'taux' => $items->count() > 0
                    ? round($items->where('statutPresence.nom_statut_presence','présent')->count() / $items->count() * 100, 1)
                    : 0,
                'couleur' => $this->getCouleurTaux(
                    round(
                        $items->where('statutPresence.nom_statut_presence','présent')->count() / max($items->count(),1) * 100,
                        1
                    )
                )
            ])->values();

        $assiduite = $presencesParMatiere->map(fn($item) => [
            'matiere' => $item['matiere'],
            'note' => round(($item['taux'] / 100) * 20, 1)
        ]);

        // Calculate dropped subjects notifications
        $droppedSubjects = $presencesParMatiere->filter(fn($item) => $item['taux'] < 30)->map(fn($item) => [
            'matiere' => $item['matiere'],
            'taux' => $item['taux'],
            'message' => "Vous êtes droppé de la matière {$item['matiere']} avec un taux de présence de {$item['taux']}%"
        ]);

        return view('etudiant.dashboardEtudiant', compact('tauxGlobal','absencesNonJustifiees','presencesParMatiere','assiduite', 'droppedSubjects'));
    }

    public function emploiDuTemps()
    {
        $etudiant = Auth::user()->etudiant;
        $classe = $etudiant->classes()->latest()->first(); // Get student's current class

        if (!$classe) {
            return view('etudiant.emploiDuTemps', [
                'emploiDuTemps' => [],
                'jours' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'],
                'error' => 'Aucune classe assignée'
            ]);
        }

        // Get all sessions for the student's class
        $seances = $classe->seances()
            ->with(['matiere', 'typeCours', 'enseignant.user', 'statutSeance'])
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

        return view('etudiant.emploiDuTemps', [
            'emploiDuTemps' => $emploiDuTemps,
            'jours' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'],
            'classe' => $classe
        ]);
    }


    public function listeAbsences()
    {
        $etudiant = Auth::user()->etudiant;
        $absences = $etudiant->presences()
            ->with('seance.matiere','justificationAbsence')
            ->whereHas('statutPresence', fn($q) => $q->where('nom_statut_presence','absent'))
            ->get();

        return view('etudiant.absences', compact('absences'));
    }

    public function noteAssiduite()
    {
        $etudiant = Auth::user()->etudiant;
        $assiduite = $etudiant->presences()
            ->with('seance.matiere','statutPresence')
            ->get()
            ->groupBy(fn($p) => $p->seance->matiere->nom_matiere)
            ->map(fn($items, $matiere) => [
                'matiere' => $matiere,
                'note' => round(($items->where('statutPresence.nom_statut_presence','présent')->count() / max($items->count(),1)) * 20, 1)
            ])->values();

        return view('etudiant.noteAssiduite', compact('assiduite'));
    }

    private function getCouleurTaux($taux)
    {
        return $taux >= 70 ? '#1a5d1a'
            : ($taux >= 50 ? '#4caf50'
            : ($taux >= 30 ? '#ff9800' : '#f44336'));
    }
}
