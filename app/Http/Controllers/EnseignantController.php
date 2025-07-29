<?php

namespace App\Http\Controllers;

use App\Models\Seance;
use App\Models\Presence;
use App\Models\StatutPresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EnseignantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('isEnseignant');
    }

    // Liste des séances à venir et passées pour l'enseignant connecté, uniquement de type "Cours"
    public function listeSeances(Request $request)
    {
        $enseignantId = Auth::user()->enseignant->id;

        $query = Seance::where('enseignant_id', $enseignantId)
            ->whereHas('typeCours', function ($q) {
                $q->where('nom_type_cours', 'Cours');
            })
            ->with(['classe', 'matiere', 'typeCours', 'statutSeance', 'presences'])
            ->orderBy('date_seance', 'desc')
            ->orderBy('heure_debut', 'asc');

        if ($request->filled('filtre')) {
            if ($request->filtre === 'avenir') {
                $query->whereDate('date_seance', '>=', Carbon::today());
            } elseif ($request->filtre === 'passe') {
                $query->whereDate('date_seance', '<', Carbon::today());
            }
        }

        $seances = $query->paginate(10);

        return view('enseignant.listeSeances', compact('seances'));
    }

    // Affiche le formulaire de saisie des présences pour une séance (cours en présentiel uniquement)
    public function formulairePresence($seanceId)
    {
        $enseignantId = Auth::user()->enseignant->id;
        $seance = Seance::with(['classe.etudiants.user', 'matiere', 'typeCours'])
            ->where('id', $seanceId)
            ->where('enseignant_id', $enseignantId)
            ->firstOrFail();

        // Autoriser la saisie uniquement pour les cours en présentiel
        if (strtolower($seance->typeCours->nom_type_cours) !== 'cours') {
            abort(403, 'La saisie des présences est autorisée uniquement pour les cours.');
        }

        $statutsPresence = StatutPresence::whereIn('nom_statut_presence', ['présent', 'retard', 'absent'])->get();

        $presences = Presence::where('seance_id', $seanceId)
            ->get()
            ->keyBy('etudiant_id');

        return view('enseignant.formulairePresence', compact('seance', 'statutsPresence', 'presences'));
    }

    // Enregistre ou met à jour les présences avec une limite de modification de 14 jours
    public function enregistrerPresence(Request $request, $seanceId)
    {
        $enseignantId = Auth::user()->enseignant->id;
        $seance = Seance::where('id', $seanceId)
            ->where('enseignant_id', $enseignantId)
            ->firstOrFail();

        $now = Carbon::now();
        $seanceDate = Carbon::parse($seance->date_seance);

        if ($now->diffInDays($seanceDate) > 14 && $now->greaterThan($seanceDate)) {
            return redirect()->back()->withErrors('La modification des présences est autorisée uniquement pendant 14 jours après la séance.');
        }

        $request->validate([
            'presences' => 'required|array',
            'presences.*.statut_presence_id' => 'required|exists:statut_presences,id',
        ]);

            foreach ($request->presences as $etudiantId => $data) {
                Presence::updateOrCreate(
                    [
                        'seance_id' => $seanceId,
                        'etudiant_id' => $etudiantId,
                    ],
                    [
                        'statut_presence_id' => $data['statut_presence_id'],
                        'enseignant_id' => $enseignantId,
                        'coordinateur_id' => $seance->coordinateur_id,
                    ]
                );
            }

        return redirect()->route('enseignant.listeSeances')->with('success', 'Présences enregistrées avec succès.');
    }

    // Affiche l'emploi du temps de l'enseignant
    public function emploiDuTemps()
    {
        $enseignantId = Auth::user()->enseignant->id;

        $seances = Seance::where('enseignant_id', $enseignantId)
            ->with(['classe', 'matiere', 'typeCours', 'statutSeance'])
            ->orderBy('date_seance', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->get();

        return view('enseignant.emploiDuTemps', compact('seances'));
    }
}
