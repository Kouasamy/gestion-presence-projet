<?php

namespace App\Http\Controllers;

use App\Http\Requests\Presence\EnregistrerPresenceRequest;
use App\Services\EnseignantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class EnseignantController extends Controller
{
    protected $enseignantService;

    /**
     * Constructeur avec injection des dépendances
     */
    public function __construct(EnseignantService $enseignantService)
    {
        $this->middleware('auth');
        $this->middleware('isEnseignant');
        $this->middleware(function ($request, $next) {
            try {
                if (Auth::check() && Auth::user()->enseignant) {
                    $etudiantsDroppes = $this->enseignantService->getEtudiantsDroppes(Auth::user()->enseignant->id);
                    View::share('etudiantsDroppes', $etudiantsDroppes);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur dans le middleware du EnseignantController: ' . $e->getMessage());
                // Ne pas partager de notifications en cas d'erreur
                View::share('etudiantsDroppes', collect([]));
            }

            return $next($request);
        });

        $this->enseignantService = $enseignantService;
    }

    /**
     * Affiche le tableau de bord de l'enseignant
     */
    public function dashboard()
    {
        return view('enseignant.dashboardEnseignant');
    }

    /**
     * Liste des séances à venir et passées pour l'enseignant connecté
     */
    public function listeSeances(Request $request)
    {
        $enseignantId = Auth::user()->enseignant->id;
        $seances = $this->enseignantService->getSeances($enseignantId, $request->all());

        return view('enseignant.listeSeances', compact('seances'));
    }

    /**
     * Affiche le formulaire de saisie des présences pour une séance
     */
    public function formulairePresence($seanceId)
    {
        $enseignantId = Auth::user()->enseignant->id;

        try {
            $seance = $this->enseignantService->getSeanceWithEtudiants($seanceId, $enseignantId);
            $statutsPresence = $this->enseignantService->getStatutsPresence();
            $presences = $this->enseignantService->getPresencesForSeance($seanceId);

            return view('enseignant.formulairePresence', compact('seance', 'statutsPresence', 'presences'));
        } catch (\Exception $e) {
            return redirect()->route('enseignant.listeSeances')->withErrors($e->getMessage());
        }
    }

    /**
     * Enregistre ou met à jour les présences
     */
    public function enregistrerPresence(EnregistrerPresenceRequest $request, $seanceId)
    {
        $enseignantId = Auth::user()->enseignant->id;

        try {
            $this->enseignantService->enregistrerPresences($seanceId, $enseignantId, $request->presences);

            return redirect()->route('enseignant.listeSeances')->with('success', 'Présences enregistrées avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Affiche l'emploi du temps de l'enseignant
     */
    public function emploiDuTemps(Request $request)
    {
        $enseignantId = Auth::user()->enseignant->id;
        $type = $request->input('type', 'tous'); // 'tous', 'passé', 'avenir'
        $emploiDuTemps = $this->enseignantService->getEmploiDuTemps($enseignantId, $type);

        // Définir les jours de la semaine
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

        return view('enseignant.emploiDuTemps', compact('emploiDuTemps', 'jours', 'type'));
    }
}
