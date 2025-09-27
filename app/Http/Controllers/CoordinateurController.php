<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmploiDuTemps\StoreEmploiDuTempsRequest;
use App\Http\Requests\EmploiDuTemps\UpdateEmploiDuTempsRequest;
use App\Http\Requests\Etudiant\AssignerClasseRequest;
use App\Http\Requests\Justification\StoreJustificationRequest;
use App\Http\Requests\Presence\StorePresenceRequest;
use App\Http\Requests\Seance\StoreSeanceRequest;
use App\Http\Requests\Seance\UpdateSeanceRequest;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Presence;
use App\Models\Seance;
use App\Models\Semestre;
use App\Models\StatutPresence;
use App\Models\StatutSeance;
use App\Models\TypeCours;
use App\Models\User;
use App\Services\EtudiantService;
use App\Services\PresenceService;
use App\Services\SeanceService;
use App\Services\StatistiqueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CoordinateurController extends Controller
{
    public function __construct(
        private SeanceService $seanceService,
        private EtudiantService $etudiantService,
        private StatistiqueService $statistiqueService,
        private PresenceService $presenceService
    ) {
        $this->middleware('auth');
        $this->middleware('isCoordinateur');
        $this->middleware(function ($request, $next) {
            try {
                if (Auth::check() && Auth::user()->role && Auth::user()->role->nom_role === 'coordinateur' && Auth::user()->coordinateur) {
                    $stats = $this->statistiqueService->getStatistiques();
                    view()->share('stats', $stats);
                }
            } catch (\Exception $e) {
                Log::error('Erreur dans le middleware du CoordinateurController: ' . $e->getMessage());
            }

            return $next($request);
        });
    }

    public function dashboard(): View
    {
        $stats = $this->statistiqueService->getStatistiques();

        return view('Coordinateur.dashboardCoordinateur', compact('stats'));
    }

    public function indexUsersByRole(string $roleName): View
    {
        $users = User::whereHas('role', function ($query) use ($roleName) {
            $query->where('nom_role', $roleName);
        })->get();

        return view("admin.gestion{$roleName}", compact('users'));
    }

    public function indexSeances(Request $request): View
    {
        $seances = $this->seanceService->getSeancesForCoordinateur($request->all());

        return view('Coordinateur.seances.index', compact('seances'));
    }

    public function createSeance(): View
    {
        $classes = Classe::all();
        $matieres = Matiere::with('cours')->get();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();
        $statutsSeance = StatutSeance::all();

        return view('Coordinateur.seances.create', compact(
            'classes',
            'matieres',
            'enseignants',
            'typesCours',
            'statutsSeance'
        ));
    }

    public function storeSeance(StoreSeanceRequest $request): RedirectResponse
    {
        $this->seanceService->createSeance($request->validated());

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'Séance créée avec succès.');
    }

    public function editSeance(Seance $seance): View
    {
        $classes = Classe::all();
        $matieres = Matiere::with('cours')->get();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();
        $statutsSeance = StatutSeance::all();

        return view('Coordinateur.seances.edit', compact(
            'seance',
            'classes',
            'matieres',
            'enseignants',
            'typesCours',
            'statutsSeance'
        ));
    }

    public function updateSeance(UpdateSeanceRequest $request, Seance $seance): RedirectResponse
    {
        $this->seanceService->updateSeance($seance, $request->validated());

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'Séance mise à jour avec succès.');
    }

    public function destroySeance(Seance $seance): RedirectResponse
    {
        $this->seanceService->deleteSeance($seance);

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'Séance supprimée avec succès.');
    }

    public function reporterSeance(Request $request, Seance $seance): RedirectResponse
    {
        $request->validate([
            'nouvelle_date' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'motif' => 'nullable|string',
        ]);

        $data = [
            'nouvelle_date' => $request->nouvelle_date,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'motif' => $request->motif,
        ];

        $this->seanceService->reporterSeance($seance, $data);

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'Séance reportée avec succès.');
    }

    public function annulerSeance(Seance $seance): RedirectResponse
    {
        $this->seanceService->annulerSeance($seance);

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'Séance annulée avec succès.');
    }

    public function statistiques(Request $request): View
    {
        try {
            $filters = $request->only(['annee', 'semestre', 'classe']);

            // Récupérer l'année académique sélectionnée ou la dernière
            $anneeId = $filters['annee'] ?? null;
            $annees = AnneeAcademique::orderBy('annee', 'desc')->get();

            // Récupérer les semestres en fonction de l'année académique sélectionnée
            if ($anneeId) {
                $semestres = Semestre::where('annees_academiques_id', $anneeId)
                    ->orderBy('date_debut_semestre')
                    ->get();
            } else {
                // Si aucune année n'est sélectionnée, prendre tous les semestres
                $semestres = Semestre::orderBy('date_debut_semestre', 'desc')->get();
            }

            $classes = Classe::orderBy('nom_classe')->get();

            // Récupérer les statistiques avec les filtres
            $stats = $this->statistiqueService->getStatistiquesDetaillees($filters);

            $tauxPresenceEtudiants = $stats['tauxPresenceEtudiants'] ?? [];
            $tauxPresenceClasses = $stats['tauxPresenceClasses'] ?? [];
            $volumeCoursParType = $stats['volumeCoursParType'] ?? [];
            $volumeCumuleCours = $stats['volumeCumuleCours'] ?? [];

            return view('Coordinateur.statistiques', compact(
                'stats',
                'annees',
                'semestres',
                'classes',
                'tauxPresenceEtudiants',
                'tauxPresenceClasses',
                'volumeCoursParType',
                'volumeCumuleCours'
            ));
        } catch (\Exception $e) {
            Log::error('Erreur dans la méthode statistiques: ' . $e->getMessage());

            $annees = AnneeAcademique::orderBy('annee', 'desc')->get();
            $semestres = Semestre::orderBy('date_debut_semestre', 'desc')->get();
            $classes = Classe::orderBy('nom_classe')->get();

            $tauxPresenceEtudiants = [
                ['nom' => 'Erreur de données', 'taux' => 0, 'couleur' => '#6b7280']
            ];
            $tauxPresenceClasses = [
                ['classe' => 'Erreur de données', 'taux' => 0]
            ];
            $volumeCoursParType = [
                'labels' => ['Erreur de données'],
                'datasets' => [
                    [
                        'label' => 'Erreur de données',
                        'data' => [0],
                        'backgroundColor' => '#6b7280'
                    ]
                ]
            ];
            $volumeCumuleCours = [
                'labels' => ['Erreur de données'],
                'datasets' => [
                    [
                        'label' => 'Erreur de données',
                        'data' => [0],
                        'borderColor' => '#6b7280',
                        'backgroundColor' => '#6b728020',
                        'fill' => false
                    ]
                ]
            ];

            return view('Coordinateur.statistiques', compact(
                'annees',
                'semestres',
                'classes',
                'tauxPresenceEtudiants',
                'tauxPresenceClasses',
                'volumeCoursParType',
                'volumeCumuleCours'
            ))->with('error', 'Une erreur est survenue lors du chargement des statistiques.');
        }
    }

    public function indexPresences(Request $request): View
    {
        $seances = $this->presenceService->getPresencesForCoordinateur($request->all());
        $classes = Classe::all();

        return view('Coordinateur.presences.index', compact('seances', 'classes'));
    }

    public function presenceForm(Seance $seance): View
    {
        $etudiants = $this->etudiantService->getEtudiantsForClasse($seance->classe_id);
        $statutsPresence = StatutPresence::all();
        $presences = $this->presenceService->getPresencesForSeance($seance->id);

        return view('Coordinateur.presenceForm', compact('seance', 'etudiants', 'statutsPresence', 'presences'));
    }

    public function storePresence(StorePresenceRequest $request, Seance $seance): RedirectResponse
    {
        $this->presenceService->storePresencesByCoordinateur(
            $seance->id,
            Auth::user()->coordinateur->id,
            $request->presences
        );

        return redirect()->route('coordinateur.presences.index')
            ->with('success', 'Présences enregistrées avec succès.');
    }

    public function indexAbsences(Request $request): View
    {
        $absences = $this->presenceService->getAbsencesForCoordinateur($request->all());
        $classes = Classe::all();

        return view('Coordinateur.absences', compact('absences', 'classes'));
    }

    public function showJustificationDetails(Presence $absence): View
    {
        $justification = $absence->justificationAbsence;

        return view('Coordinateur.justificationDetails', compact('absence', 'justification'));
    }

    public function justifyAbsence(Request $request, Presence $absence): RedirectResponse
    {
        $request->validate([
            'motif' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'date_justification' => 'required|date',
        ]);

        $data = $request->only(['motif', 'date_justification']);

        if ($request->hasFile('document')) {
            $data['document'] = $request->file('document');
        }

        $this->presenceService->justifyAbsence($absence->id, $data);

        return redirect()->route('coordinateur.absences.index')
            ->with('success', 'Absence justifiée avec succès.');
    }

    public function createJustification(Presence $presence): View
    {
        return view('Coordinateur.justificationCreate', compact('presence'));
    }

    public function storeJustification(StoreJustificationRequest $request, Presence $presence): RedirectResponse
    {
        $this->presenceService->storeJustification($presence->id, $request->validated());

        return redirect()->route('coordinateur.absences.index')
            ->with('success', 'Justification enregistrée avec succès.');
    }

    public function indexEmploiDuTemps(Request $request): View
    {
        $classes = Classe::all();
        $filter = $request->input('filter');

        // Récupérer toutes les séances pour toutes les classes
        $query = Seance::with(['classe', 'matiere', 'enseignant.user', 'typeCours', 'statutSeance'])
            ->where('coordinateur_id', Auth::user()->coordinateur->id)
            ->orderBy('date_seance')
            ->orderBy('heure_debut');

        // Organiser les séances par semaine et par classe
        $emploisDuTempsParClasse = [];
        $maintenant = Carbon::now();

        // Récupérer les séances
        $seances = $query->get();

        foreach ($seances as $seance) {
            $classeId = $seance->classe_id;
            $classeNom = $seance->classe->nom_classe;
            $dateSeance = Carbon::parse($seance->date_seance);
            $numeroSemaine = $dateSeance->weekOfYear;
            $debutSemaine = $dateSeance->copy()->startOfWeek()->format('Y-m-d');
            $finSemaine = $dateSeance->copy()->endOfWeek()->format('Y-m-d');

            // Déterminer le statut de la semaine
            $estPassee = $dateSeance->endOfWeek() < $maintenant;
            $estCourante = $dateSeance->startOfWeek() <= $maintenant && $dateSeance->endOfWeek() >= $maintenant;
            $estFuture = $dateSeance->startOfWeek() > $maintenant;

            // Appliquer le filtre
            if ($filter === 'passe' && !$estPassee) {
                continue;
            } elseif ($filter === 'en_cours' && !$estCourante) {
                continue;
            } elseif ($filter === 'a_venir' && !$estFuture) {
                continue;
            }

            if (!isset($emploisDuTempsParClasse[$classeId])) {
                $emploisDuTempsParClasse[$classeId] = [
                    'classe' => $classeNom,
                    'classe_id' => $classeId,
                    'coordinateur' => Auth::user()->nom,
                    'semaines' => []
                ];
            }

            // Vérifier si cette semaine existe déjà pour cette classe
            $semaineExiste = false;
            foreach ($emploisDuTempsParClasse[$classeId]['semaines'] as $semaine) {
                if ($semaine['debut_semaine'] === $debutSemaine && $semaine['fin_semaine'] === $finSemaine) {
                    $semaineExiste = true;
                    break;
                }
            }

            // Ajouter la semaine si elle n'existe pas encore
            if (!$semaineExiste) {
                $emploisDuTempsParClasse[$classeId]['semaines'][] = [
                    'debut_semaine' => $debutSemaine,
                    'fin_semaine' => $finSemaine,
                    'est_passee' => $estPassee,
                    'est_courante' => $estCourante,
                    'est_future' => $estFuture
                ];
            }
        }

        $emploisDuTempsCollection = collect($emploisDuTempsParClasse);

        return view('Coordinateur.listeEmploiDuTemps', compact('classes', 'emploisDuTempsCollection'));
    }

    public function createEmploiDuTemps(): View
    {
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();

        return view('Coordinateur.createEmploiDuTemps', compact(
            'classes',
            'matieres',
            'enseignants',
            'typesCours'
        ));
    }

    public function storeEmploiDuTemps(StoreEmploiDuTempsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['horaires'])) {
            foreach ($data['seances'] as $key => $seance) {
                $periode = $seance['periode'];
                if (isset($data['horaires'][$periode])) {
                    $data['seances'][$key]['heure_debut'] = $data['horaires'][$periode]['heure_debut'];
                    $data['seances'][$key]['heure_fin'] = $data['horaires'][$periode]['heure_fin'];
                }
            }
        }

        $this->seanceService->createEmploiDuTemps($data);

        return redirect()->route('coordinateur.emploiDuTemps.index')
            ->with('success', 'Emploi du temps créé avec succès.');
    }

    public function editEmploiDuTemps(int $classeId, Request $request): View
    {
        $classe = Classe::findOrFail($classeId);
        $matieres = Matiere::all();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();

        $dateDebut = $request->input('date_debut');

        $query = Seance::where('classe_id', $classeId)
            ->with(['matiere', 'enseignant.user', 'typeCours', 'statutSeance'])
            ->orderBy('date_seance')
            ->orderBy('heure_debut');

        if ($dateDebut) {
            $dateDebutObj = Carbon::parse($dateDebut);
            $dateFinObj = $dateDebutObj->copy()->addDays(6);
            $query->whereDate('date_seance', '>=', $dateDebutObj->format('Y-m-d'));
            $query->whereDate('date_seance', '<=', $dateFinObj->format('Y-m-d'));
        }

        $seances = $query->get();

        return view('Coordinateur.editEmploiDuTemps', compact(
            'classe',
            'matieres',
            'enseignants',
            'typesCours',
            'seances',
            'dateDebut'
        ));
    }

    public function updateEmploiDuTemps(UpdateEmploiDuTempsRequest $request, int $classeId): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['horaires'])) {
            foreach ($data['seances'] as $key => $seance) {
                $periode = $seance['periode'];
                if (isset($data['horaires'][$periode])) {
                    $data['seances'][$key]['heure_debut'] = $data['horaires'][$periode]['heure_debut'];
                    $data['seances'][$key]['heure_fin'] = $data['horaires'][$periode]['heure_fin'];
                }
            }
        }

        $this->seanceService->updateEmploiDuTemps($classeId, $data);

        return redirect()->route('coordinateur.emploiDuTemps.index')
            ->with('success', 'Emploi du temps mis à jour avec succès.');
    }

    public function emploiDuTempsParClasse(int $classeId, Request $request): View
    {
        $classe = Classe::findOrFail($classeId);
        $dateDebut = $request->input('date_debut');

        if (!$dateDebut) {
            $dateDebut = Carbon::now()->startOfWeek()->format('Y-m-d');
        }

        $emploiDuTemps = $this->seanceService->getEmploiDuTempsForClasse($classe->id, $dateDebut);
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
        $classes = Classe::all();

        return view('Coordinateur.emploiDuTemps', compact('classe', 'emploiDuTemps', 'dateDebut', 'jours', 'classes'));
    }

    public function destroyEmploiDuTemps(int $classeId, Request $request): RedirectResponse
    {
        $dateDebut = $request->input('date_debut');
        $this->seanceService->deleteEmploiDuTemps($classeId, $dateDebut);

        return redirect()->route('coordinateur.emploiDuTemps.index')
            ->with('success', 'Emploi du temps supprimé avec succès.');
    }

    public function listeEtudiants(Request $request): View
    {
        $etudiants = $this->etudiantService->getEtudiants($request->all());
        $classes = Classe::all();

        return view('Coordinateur.listeEtudiants', compact('etudiants', 'classes'));
    }

    public function formAssignerClasse(Etudiant $etudiant): View
    {
        $classes = Classe::all();
        $annees = AnneeAcademique::all();

        return view('Coordinateur.formAssignerClasse', compact('etudiant', 'classes', 'annees'));
    }

    public function assignerClasse(AssignerClasseRequest $request, Etudiant $etudiant): RedirectResponse
    {
        $this->etudiantService->assignerClasse(
            $etudiant->id,
            $request->classe_id,
            $request->annee_academique_id
        );

        return redirect()->route('coordinateur.etudiants.index')
            ->with('success', 'Étudiant assigné à la classe avec succès.');
    }

    public function desinscrireClasse(Etudiant $etudiant, Classe $classe): RedirectResponse
    {
        $this->etudiantService->desinscrireClasse($etudiant->id, $classe->id);

        return redirect()->route('coordinateur.etudiants.index')
            ->with('success', 'Étudiant désinscrit de la classe avec succès.');
    }
}
