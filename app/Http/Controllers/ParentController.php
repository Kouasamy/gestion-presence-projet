<?php

namespace App\Http\Controllers;

use App\Services\ParentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ParentController extends Controller
{
    protected $parentService;

    /**
     * Constructeur avec injection des dépendances
     */
    public function __construct(ParentService $parentService)
    {
        $this->middleware('auth');
        $this->middleware('isParent');
        $this->middleware(function ($request, $next) {
            try {
                if (Auth::check() && Auth::user()->parent) {
                    $data = $this->parentService->getStatistiquesEtNotifications(Auth::user()->parent->id);
                    View::share('notifications', $data['notifications'] ?? collect([]));
                } else {
                    View::share('notifications', collect([]));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur dans le middleware du ParentController: ' . $e->getMessage());
                // Ne pas partager de notifications en cas d'erreur
                View::share('notifications', collect([]));
            }

            return $next($request);
        });

        $this->parentService = $parentService;
    }

    /**
     * Affiche le tableau de bord du parent
     */
    public function dashboard()
    {
        $parent = Auth::user()->parent;

        if (! $parent) {
            return view('parent.dashboardParent', [
                'etudiants' => collect(),
                'totalAbsences' => 0,
                'justifiees' => 0,
                'nonJustifiees' => 0,
                'tauxPresence' => 0,
            ]);
        }

        $data = $this->parentService->getStatistiquesEtNotifications($parent->id);
        // Supprimer les notifications car elles sont déjà partagées avec toutes les vues
        unset($data['notifications']);

        return view('parent.dashboardParent', $data);
    }

    /**
     * Affiche l'emploi du temps des enfants du parent connecté
     */
    public function emploiDuTemps()
    {
        $parent = Auth::user()->parent;

        if (! $parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Aucun parent associé trouvé.');
        }

        $etudiants = $this->parentService->getEmploiDuTemps($parent->id);
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

        // Formater les données pour chaque étudiant
        $etudiantsEmploiDuTemps = [];

        foreach ($etudiants as $etudiant) {
            $emploiDuTempsEtudiant = [];

            foreach ($etudiant->classes as $classe) {
                // Formater les séances pour cette classe
                $emploiDuTempsClasse = [];

                foreach ($classe->seances as $seance) {
                    $date = \Carbon\Carbon::parse($seance->date_seance);
                    $jour = ucfirst($date->locale('fr')->dayName);
                    $heureDebut = \Carbon\Carbon::parse($seance->heure_debut);
                    $periode = ($heureDebut->hour < 12) ? 'matin' : 'soir';

                    $emploiDuTempsClasse[$jour][$periode] = [
                        'id' => $seance->id,
                        'cours' => $seance->matiere->nom_matiere,
                        'enseignant' => $seance->enseignant->user->nom,
                        'type' => $seance->typeCours->nom_type_cours,
                        'heure_debut' => $seance->heure_debut,
                        'heure_fin' => $seance->heure_fin,
                        'statut_id' => $seance->statut_seance_id,
                        'statut' => $seance->statutSeance ? $seance->statutSeance->nom_statut : 'Planifiée',
                        'date_seance' => $seance->date_seance,
                    ];
                }

                $emploiDuTempsEtudiant[] = [
                    'classe' => $classe,
                    'emploiDuTemps' => $emploiDuTempsClasse
                ];
            }

            $etudiantsEmploiDuTemps[] = [
                'etudiant' => $etudiant,
                'emploiDuTemps' => $emploiDuTempsEtudiant
            ];
        }

        // Récupérer la semaine en cours pour l'affichage
        $maintenant = \Carbon\Carbon::now();
        $debutSemaine = $maintenant->copy()->startOfWeek()->format('d/m/Y');
        $finSemaine = $maintenant->copy()->endOfWeek()->format('d/m/Y');

        return view('parent.emploiDuTemps', compact('etudiantsEmploiDuTemps', 'jours', 'debutSemaine', 'finSemaine'));
    }

    /**
     * Affiche les absences des enfants du parent connecté
     */
    public function absences()
    {
        $parent = Auth::user()->parent;

        if (! $parent) {
            return redirect()->route('parent.dashboard')->with('error', 'Aucun parent associé trouvé.');
        }

        $absencesData = $this->parentService->getAbsences($parent->id);

        return view('parent.absences', compact('absencesData'));
    }
}
