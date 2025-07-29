<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\JustificationAbsence;
use App\Models\Matiere;
use App\Models\Presence;
use App\Models\Seance;
use App\Models\StatutPresence;
use App\Models\StatutSeance;
use App\Models\TypeCours;
use App\Models\Semestre;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoordinateurController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('isCoordinateur');
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role->nom_role === 'coordinateur') {
                $stats = $this->getStatistiques();
                view()->share('stats', $stats);
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = $this->getStatistiques();
        return view('coordinateur.dashboardCoordinateur', compact('stats'));
    }



    public function indexUsersByRole($roleName)
    {
        $users = User::whereHas('role', function ($query) use ($roleName) {
            $query->where('nom_role', $roleName);
        })->get();

        return view("admin.gestion{$roleName}", compact('users'));
    }

    public function createSeance()
    {
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();
        $statutsSeance = StatutSeance::all();

        return view('coordinateur.createSeance', compact('classes', 'matieres', 'enseignants', 'typesCours', 'statutsSeance'));
    }

    public function storeSeance(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'type_cours_id' => 'required|exists:type_cours,id',
            'statut_seance_id' => 'required|exists:statut_seances,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required',
            'heure_fin' => 'required|after:heure_debut',
        ]);

        Seance::create([
            'classe_id' => $request->classe_id,
            'matiere_id' => $request->matiere_id,
            'enseignant_id' => $request->enseignant_id,
            'type_cours_id' => $request->type_cours_id,
            'statut_seance_id' => $request->statut_seance_id,
            'date_seance' => $request->date_seance,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'coordinateur_id' => Auth::user()->coordinateur->id,
        ]);

        return redirect()->route('coordinateur.seances.index')->with('success', 'Séance créée avec succès.');
    }

    public function indexSeances(Request $request)
    {
        $query = Seance::where('coordinateur_id', Auth::user()->coordinateur->id)
            ->with(['classe', 'matiere', 'enseignant.user', 'typeCours', 'statutSeance']);

        // Si on demande la vue emploi du temps
        if ($request->view === 'timetable') {
            $classes = Classe::all();
            return view('coordinateur.emploiDuTemps', compact('classes'));
        }

        // Filtre par type (attendance ou justification)
        if ($request->filter === 'attendance') {
            $query->whereHas('presences', function ($q) {
                $q->whereDoesntHave('justificationAbsence');
            });
        } elseif ($request->filter === 'justification') {
            $query->whereHas('presences', function ($q) {
                $q->whereHas('justificationAbsence');
            });
        }

        // Filtre par classe
        if ($request->filled('classe')) {
            $query->where('classe_id', $request->classe);
        }

        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('date_seance', $request->date);
        }

        // Filtre par type de cours
        if ($request->filled('type')) {
            $query->where('type_cours_id', $request->type);
        }

        $seances = $query->orderBy('date_seance', 'desc')
            ->orderBy('heure_debut', 'asc')
            ->get();

        $classes = Classe::all();
        $typesCours = TypeCours::all();

        return view('coordinateur.listeSeances', compact('seances', 'classes', 'typesCours'));
    }

    public function editSeance($id)
    {
        $seance = Seance::findOrFail($id);
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();
        $statutsSeance = StatutSeance::all();

        return view('coordinateur.editSeance', compact('seance', 'classes', 'matieres', 'enseignants', 'typesCours', 'statutsSeance'));
    }

    public function updateSeance(Request $request, $id)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'type_cours_id' => 'required|exists:type_cours,id',
            'statut_seance_id' => 'required|exists:statut_seances,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required',
            'heure_fin' => 'required|after:heure_debut',
        ]);

        $seance = Seance::findOrFail($id);
        $seance->update($request->all());

        return redirect()->route('coordinateur.seances.index')->with('success', 'Séance modifiée avec succès.');
    }

    public function destroySeance($id)
    {
        $seance = Seance::findOrFail($id);
        $seance->delete();

        return redirect()->route('coordinateur.seances.index')->with('success', 'Séance supprimée avec succès.');
    }

    public function emploiDuTempsParClasse($classeId)
    {
        $dateDebut = request('date_debut');
        $classe = Classe::with(['seances' => function ($query) use ($dateDebut) {
            $query->orderBy('date_seance')->orderBy('heure_debut');
            if ($dateDebut) {
                $dateDebutObj = Carbon::parse($dateDebut);
                $dateFinObj = $dateDebutObj->copy()->addDays(6);
                $query->whereDate('date_seance', '>=', $dateDebutObj->format('Y-m-d'));
                $query->whereDate('date_seance', '<=', $dateFinObj->format('Y-m-d'));
            }
        }, 'seances.matiere', 'seances.enseignant.user', 'seances.typeCours'])
            ->findOrFail($classeId);

        return view('coordinateur.emploiDuTemps', compact('classe', 'dateDebut'));
    }



    public function presenceForm($seanceId)
    {
        $seance = Seance::with(['classe.etudiants.user', 'matiere', 'typeCours'])
            ->findOrFail($seanceId);
        $statutsPresence = StatutPresence::all();
        $presences = Presence::where('seance_id', $seanceId)
            ->get()
            ->keyBy('etudiant_id');

        return view('coordinateur.presenceForm', compact('seance', 'statutsPresence', 'presences'));
    }

    public function storePresence(Request $request, $seanceId)
    {
        $request->validate([
            'presences' => 'required|array',
            'presences.*.statut_presence_id' => 'required|exists:statut_presences,id',
            'presences.*.justification' => 'nullable|string|max:255',
        ]);

        foreach ($request->presences as $etudiantId => $data) {
            // On met à jour ou crée la présence (sans justification)
            $presence = Presence::updateOrCreate(
                [
                    'seance_id' => $seanceId,
                    'etudiant_id' => $etudiantId
                ],
                [
                    'statut_presence_id' => $data['statut_presence_id'],
                    'coordinateur_id' => Auth::user()->coordinateur->id
                ]
            );

            // Si une justification est fournie, on la crée dans la table justification_absences
            if (!empty($data['justification'])) {
                // On évite de dupliquer une justification si elle existe déjà
                if (!$presence->justificationAbsence) {
                    JustificationAbsence::create([
                        'presence_id' => $presence->id,
                        'motif' => $data['justification'],
                        'date_justification' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('coordinateur.presences.index')
            ->with('success', 'Présences enregistrées avec succès.');
    }

    public function createJustification($presenceId)
    {
        $presence = Presence::with(['etudiant.user', 'seance.classe', 'seance.matiere'])
            ->findOrFail($presenceId);

        return view('coordinateur.justificationCreate', compact('presence'));
    }

    public function storeJustification(Request $request, $presenceId)
    {
        $request->validate([
            'motif' => 'required|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $presence = Presence::findOrFail($presenceId);

        if ($presence->justificationAbsence) {
            return back()->with('error', 'Cette absence a déjà été justifiée.');
        }

        $justification = new JustificationAbsence([
            'presence_id' => $presence->id,
            'motif' => $request->motif,
            'date_justification' => now()
        ]);

        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('justifications', 'public');
            $justification->document_path = $path;
        }

        $justification->save();

        return redirect()->route('coordinateur.absences.index')
            ->with('success', 'Justification enregistrée avec succès.');
    }

    public function createEmploiDuTemps()
    {
        $matieres = Matiere::all();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();
        $classes = Classe::all();

        return view('coordinateur.createEmploiDuTemps', compact('matieres', 'enseignants', 'typesCours', 'classes'));
    }

    public function storeEmploiDuTemps(Request $request)
    {
        try {
            $request->validate([
                'classe_id' => 'required|exists:classes,id',
                'seances' => 'required|array',
                'seances.*.matiere_id' => 'required|exists:matieres,id',
                'seances.*.enseignant_id' => 'required|exists:enseignants,id',
                'seances.*.type_cours_id' => 'required|exists:type_cours,id',
                'seances.*.date_seance' => 'required|date',
                'seances.*.periode' => 'required|in:matin,soir'
            ]);

            $coordinateur = Auth::user()->coordinateur;
            if (!$coordinateur) {
                return redirect()->back()
                    ->withErrors(['message' => 'Utilisateur non autorisé'])
                    ->withInput();
            }

            DB::beginTransaction();
            try {
                $classeId = $request->classe_id;

                foreach ($request->seances as $key => $seanceData) {
                    if (!empty($seanceData['matiere_id']) && !empty($seanceData['date_seance'])) {
                        $heureDebut = $seanceData['periode'] === 'matin' ? '09:00' : '13:30';
                        $heureFin = $seanceData['periode'] === 'matin' ? '12:00' : '16:30';

                        // Vérifier les conflits d'horaire pour la classe (en excluant les séances annulées ou reportées)
                        $conflitClasse = Seance::where('classe_id', $classeId)
                            ->where('date_seance', $seanceData['date_seance'])
                            ->whereNotIn('statut_seance_id', [3, 4]) // Exclure les séances annulées (3) et reportées (4)
                            ->where(function ($query) use ($heureDebut, $heureFin) {
                                $query->whereBetween('heure_debut', [$heureDebut, $heureFin])
                                    ->orWhereBetween('heure_fin', [$heureDebut, $heureFin])
                                    ->orWhere(function ($q) use ($heureDebut, $heureFin) {
                                        $q->where('heure_debut', '<=', $heureDebut)
                                            ->where('heure_fin', '>=', $heureFin);
                                    });
                            })->first();

                        if ($conflitClasse) {
                            throw new \Exception("Conflit d'horaire pour la classe à la date {$seanceData['date_seance']} ({$seanceData['periode']}) - Une séance est déjà programmée à cet horaire");
                        }

                        // Vérifier les conflits d'horaire pour l'enseignant (en excluant les séances annulées ou reportées)
                        $conflitEnseignant = Seance::where('enseignant_id', $seanceData['enseignant_id'])
                            ->where('date_seance', $seanceData['date_seance'])
                            ->whereNotIn('statut_seance_id', [3, 4]) // Exclure les séances annulées (3) et reportées (4)
                            ->where(function ($query) use ($heureDebut, $heureFin) {
                                $query->whereBetween('heure_debut', [$heureDebut, $heureFin])
                                    ->orWhereBetween('heure_fin', [$heureDebut, $heureFin])
                                    ->orWhere(function ($q) use ($heureDebut, $heureFin) {
                                        $q->where('heure_debut', '<=', $heureDebut)
                                            ->where('heure_fin', '>=', $heureFin);
                                    });
                            })->first();

                        if ($conflitEnseignant) {
                            throw new \Exception("Conflit d'horaire pour l'enseignant à la date {$seanceData['date_seance']} ({$seanceData['periode']})");
                        }

                        // Créer la séance
                        Seance::create([
                            'classe_id' => $classeId,
                            'matiere_id' => $seanceData['matiere_id'],
                            'enseignant_id' => $seanceData['enseignant_id'],
                            'type_cours_id' => $seanceData['type_cours_id'],
                            'coordinateur_id' => $coordinateur->id,
                            'date_seance' => $seanceData['date_seance'],
                            'heure_debut' => $heureDebut,
                            'heure_fin' => $heureFin,
                            'statut_seance_id' => 1 // Statut par défaut
                        ]);
                    }
                }

                DB::commit();
                return redirect()->route('coordinateur.emploiDuTemps.index')
                    ->with('success', 'Emploi du temps créé avec succès');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                    ->withErrors(['message' => $e->getMessage()])
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['message' => $e->getMessage()])
                ->withInput();
        }
    }

    public function updateEmploiDuTemps(Request $request, $emploiId)
    {
        try {
            $request->validate([
                'seances' => 'required|array',
                'seances.*.id' => 'required|exists:seances,id',
                'seances.*.classe_id' => 'required|exists:classes,id',
                'seances.*.matiere_id' => 'required|exists:matieres,id',
                'seances.*.enseignant_id' => 'required|exists:enseignants,id',
                'seances.*.type_cours_id' => 'required|exists:type_cours,id',
                'seances.*.date_seance' => 'required|date',
                'seances.*.periode' => 'required|in:matin,soir'
            ]);

            DB::beginTransaction();
            try {
                foreach ($request->seances as $seanceData) {
                    $heureDebut = $seanceData['periode'] === 'matin' ? '09:00' : '13:30';
                    $heureFin = $seanceData['periode'] === 'matin' ? '12:00' : '16:30';

                    $seance = Seance::findOrFail($seanceData['id']);
                    $seance->update([
                        'classe_id' => $seanceData['classe_id'],
                        'matiere_id' => $seanceData['matiere_id'],
                        'enseignant_id' => $seanceData['enseignant_id'],
                        'type_cours_id' => $seanceData['type_cours_id'],
                        'date_seance' => $seanceData['date_seance'],
                        'heure_debut' => $heureDebut,
                        'heure_fin' => $heureFin
                    ]);
                }

                DB::commit();
                return redirect()->route('coordinateur.emploiDuTemps.index')
                    ->with('success', 'Emploi du temps mis à jour avec succès');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()
                    ->withErrors(['message' => $e->getMessage()])
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['message' => $e->getMessage()])
                ->withInput();
        }
    }

    public function indexPresences(Request $request)
    {
        $query = Seance::where('coordinateur_id', Auth::user()->coordinateur->id)
            ->whereDate('date_seance', '<=', now())
            ->with(['classe', 'presences.etudiant.user'])
            ->orderBy('date_seance', 'desc');

        // Filtre par classe
        if ($request->filled('classe')) {
            $query->where('classe_id', $request->classe);
        }

        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('date_seance', $request->date);
        }

        $seances = $query->paginate(15);
        $classes = Classe::all();

        return view('coordinateur.presences.index', compact('seances', 'classes'));
    }

    public function indexAbsences(Request $request)
    {
        $query = Presence::whereHas('seance', function ($q) {
            $q->where('coordinateur_id', Auth::user()->coordinateur->id);
        })
            ->where('statut_presence_id', 2)
            ->with(['seance.classe', 'seance.matiere', 'etudiant.user', 'justificationAbsence']);

        // Filtre par classe
        if ($request->filled('classe')) {
            $query->whereHas('seance', function ($q) use ($request) {
                $q->where('classe_id', $request->classe);
            });
        }

        // Filtre par statut de justification
        if ($request->filled('status')) {
            if ($request->status === 'justifiee') {
                $query->whereHas('justificationAbsence');
            } elseif ($request->status === 'non_justifiee') {
                $query->whereDoesntHave('justificationAbsence');
            }
        }

        // Filtre par date
        if ($request->filled('date_debut')) {
            $query->whereHas('seance', function ($q) use ($request) {
                $q->whereDate('date_seance', '>=', $request->date_debut);
            });
        }
        if ($request->filled('date_fin')) {
            $query->whereHas('seance', function ($q) use ($request) {
                $q->whereDate('date_seance', '<=', $request->date_fin);
            });
        }

        $absences = $query->orderBy('created_at', 'desc')->paginate(15);
        $classes = Classe::all();

        return view('coordinateur.absences', compact('absences', 'classes'));
    }

    public function justifyAbsence(Request $request, $absenceId)
    {
        $request->validate([
            'motif' => 'required|string|max:255',
            'date_justification' => 'required|date'
        ]);

        $absence = Presence::findOrFail($absenceId);

        // Vérifier que l'absence appartient à une séance gérée par ce coordinateur
        if ($absence->seance->coordinateur_id !== Auth::user()->coordinateur->id) {
            return back()->with('error', 'Non autorisé');
        }

        // Vérifier que l'absence n'est pas déjà justifiée
        if ($absence->justificationAbsence) {
            return back()->with('error', 'Cette absence a déjà été justifiée');
        }

        // Créer la justification
        JustificationAbsence::create([
            'presence_id' => $absence->id,
            'motif' => $request->motif,
            'date_justification' => $request->date_justification
        ]);

        return redirect()->route('coordinateur.absences.index')
            ->with('success', 'Justification enregistrée avec succès');
    }

    public function showJustificationDetails($absenceId)
    {
        $absence = Presence::with([
            'seance.classe',
            'seance.matiere',
            'etudiant.user',
            'justificationAbsence'
        ])->findOrFail($absenceId);

        // Vérifier que l'absence appartient à une séance gérée par ce coordinateur
        if ($absence->seance->coordinateur_id !== Auth::user()->coordinateur->id) {
            abort(403);
        }

        return view('coordinateur.justificationDetails', compact('absence'));
    }

    public function indexEmploiDuTemps(Request $request)
    {
        $query = Seance::where('coordinateur_id', Auth::user()->coordinateur->id)
            ->select('date_seance', 'classe_id', 'coordinateur_id')
            ->with(['classe', 'coordinateur.user'])
            ->groupBy('date_seance', 'classe_id', 'coordinateur_id');

        // Filtre par classe
        if ($request->filled('classe')) {
            $query->where('classe_id', $request->classe);
        }

        // Filtre par date
        if ($request->filled('date_debut')) {
            $query->whereDate('date_seance', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_seance', '<=', $request->date_fin);
        }

        $emploisDuTemps = $query->orderBy('date_seance', 'desc')->paginate(10);
        $classes = Classe::all();

        return view('coordinateur.listeEmploiDuTemps', compact('emploisDuTemps', 'classes'));
    }



    /**
     * Affiche la liste des étudiants avec photo et informations, filtrable par classe.
     */
    public function listeEtudiants(Request $request)
    {
        $classes = Classe::all();
        $annees = AnneeAcademique::all();
        $query = Etudiant::with(['user', 'classes']);
        if ($request->filled('classe')) {
            $query->whereHas('classes', function ($q) use ($request) {
                $q->where('classes.id', $request->classe);
            });
        }
        $etudiants = $query->get();

        // Calcul du taux de présence pour chaque étudiant (optionnel)
        foreach ($etudiants as $etudiant) {
            $total = Presence::where('etudiant_id', $etudiant->id)->count();
            $present = Presence::where('etudiant_id', $etudiant->id)
                ->whereHas('statutPresence', function ($q) {
                    $q->where('nom_statut_presence', 'présent');
                })->count();
            $etudiant->taux_presence = $total > 0 ? round($present / $total * 100, 1) : null;
        }

        return view('Coordinateur.listeEtudiants', compact('etudiants', 'classes', 'annees'));
    }

    /**
     * Assigne un étudiant à une classe (avec année académique et date de début)
     */
    public function assignerClasse(Request $request, $etudiantId)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
            'date_debut' => 'required|date',
        ]);

        $etudiant = Etudiant::findOrFail($etudiantId);
        $classeId = $request->classe_id;
        $anneeId = $request->annee_academique_id;
        $dateDebut = $request->date_debut;

        // Vérifier si déjà assigné à cette classe pour cette année
        $dejaAssigne = $etudiant->classes()->wherePivot('classe_id', $classeId)
            ->wherePivot('annee_academique_id', $anneeId)
            ->exists();
        if ($dejaAssigne) {
            return back()->with('error', 'Cet étudiant est déjà assigné à cette classe pour cette année.');
        }

        $etudiant->classes()->attach($classeId, [
            'annee_academique_id' => $anneeId,
            'date_debut' => $dateDebut,
            'date_fin' => null,
        ]);

        return back()->with('success', 'Étudiant assigné à la classe avec succès.');
    }

    /**
     * Désinscrire un étudiant d'une classe (supprime la ligne de la table de pivot)
     */
    public function desinscrireClasse(Request $request, $etudiantId, $classeId)
    {
        $etudiant = Etudiant::findOrFail($etudiantId);
        $etudiant->classes()->detach($classeId);
        return back()->with('success', 'Étudiant désinscrit de la classe avec succès.');
    }

    /**
     * Affiche le formulaire d'assignation d'un étudiant à une classe
     */
    public function formAssignerClasse($etudiantId)
    {
        $etudiant = Etudiant::with('user', 'classes')->findOrFail($etudiantId);
        $classes = Classe::all();
        $annees = AnneeAcademique::all();
        return view('Coordinateur.formAssignerClasse', compact('etudiant', 'classes', 'annees'));
    }

    // Méthodes CRUD pour les séances
    public function index()
    {
        $seances = Seance::with(['classe', 'matiere', 'enseignant.user', 'typeCours', 'statutSeance'])
            ->orderBy('date_seance', 'desc')
            ->paginate(10);

        return view('coordinateur.seances.index', compact('seances'));
    }

    public function create()
    {
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();
        $statutsSeance = StatutSeance::all();

        return view('coordinateur.seances.create', compact(
            'classes',
            'matieres',
            'enseignants',
            'typesCours',
            'statutsSeance'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'type_cours_id' => 'required|exists:type_cours,id',
            'statut_seance_id' => 'required|exists:statut_seances,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->withErrors('Aucun utilisateur connecté.');
        }

        $coordinateur = $user->coordinateur;
        if (!$coordinateur) {
            return redirect()->back()->withErrors('Aucun coordinateur associé à cet utilisateur.');
        }

        $seance = Seance::create($validated + [
            'coordinateur_id' => $coordinateur->id,
        ]);

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'Séance créée avec succès.');
    }

    public function edit(Seance $seance)
    {
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();
        $statutsSeance = StatutSeance::all();

        return view('coordinateur.seances.edit', compact(
            'seance',
            'classes',
            'matieres',
            'enseignants',
            'typesCours',
            'statutsSeance'
        ))->with('types', $typesCours);
    }

    public function update(Request $request, Seance $seance)
    {
        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'type_cours_id' => 'required|exists:type_cours,id',
            'statut_seance_id' => 'required|exists:statut_seances,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $seance->update($validated);

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'Séance mise à jour avec succès.');
    }

    public function destroy(Seance $seance)
    {
        $seance->delete();

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'Séance supprimée avec succès.');
    }

    public function reporterSeance(Request $request, $seanceId)
    {
        $request->validate([
            'nouvelle_date' => 'required|date|after:today',
        ]);

        $seance = Seance::findOrFail($seanceId);

        $user = Auth::user();

        if (!$user || !$user->coordinateur) {
            return redirect()->back()->withErrors('Seul un coordinateur peut reporter une séance.');
        }

        // Sauvegarder l'historique du report
        $seance->historique_reports()->create([
            'date_initiale' => $seance->date_seance,
            'heure_debut_initiale' => $seance->heure_debut,
            'heure_fin_initiale' => $seance->heure_fin,
            'coordinateur_id' => $user->coordinateur->id,
        ]);

        // Mettre à jour la séance
        $seance->update([
            'date_seance' => $request->nouvelle_date,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'statut_seance_id' => 4, // ID pour "Reportée"
        ]);

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'La séance a été reportée avec succès.');
    }


    public function annulerSeance(Request $request, $seanceId)
    {
        $seance = Seance::findOrFail($seanceId);

        $user = Auth::user();

        if (!$user || !$user->coordinateur) {
            return redirect()->back()->withErrors('Seul un coordinateur peut annuler une séance.');
        }


        $seance->update([
            'statut_seance_id' => 3, // ID pour "Annulée"
        ]);

        return redirect()->route('coordinateur.seances.index')
            ->with('success', 'La séance a été annulée avec succès.');
    }

    public function editEmploiDuTemps($emploiId)
    {
        // À adapter selon ta structure :
        $seances = Seance::where('classe_id', $emploiId)->get();
        $classe = Classe::findOrFail($emploiId);
        $matieres = Matiere::all();
        $enseignants = Enseignant::with('user')->get();
        $typesCours = TypeCours::all();
        return view('coordinateur.editEmploiDuTemps', compact('classe', 'seances', 'matieres', 'enseignants', 'typesCours'));
    }

    private function getStatistiques()
    {
        // Nombre de séances prévues aujourd'hui
        $seancesAujourdhui = Seance::where('coordinateur_id', Auth::user()->coordinateur->id)
            ->whereDate('date_seance', Carbon::today())
            ->count();

        // Nombre de séances sans présences saisies
        $presencesASaisir = Seance::where('coordinateur_id', Auth::user()->coordinateur->id)
            ->whereDoesntHave('presences')
            ->count();

        // Nombre d'absences non justifiées
        $absencesNonJustifiees = Presence::whereHas('seance', function ($q) {
            $q->where('coordinateur_id', Auth::user()->coordinateur->id);
        })
            ->where('statut_presence_id', 2) // 2 = Absent
            ->whereDoesntHave('justificationAbsence')
            ->count();

        // Taux de présence par étudiant
        $tauxPresenceEtudiants = Etudiant::with('user')
            ->whereHas('presences')
            ->get()
            ->map(function ($etudiant) {
                $total = $etudiant->presences()->count();
                $absents = $etudiant->presences()
                    ->where('statut_presence_id', 2)
                    ->count();

                $taux = $total > 0 ? round(100 - ($absents / $total * 100), 1) : 0;
                $couleur = $this->getCouleurTaux($taux);

                return [
                    'id' => $etudiant->id,
                    'nom' => $etudiant->user->nom,
                    'photo' => $etudiant->user->photo_url ?? 'https://randomuser.me/api/portraits/men/' . ($etudiant->id % 100) . '.jpg',
                    'taux' => $taux,
                    'couleur' => $couleur,
                    'dropped' => $taux < 30
                ];
            });

        // Étudiants avec taux < 30%
        $etudiantsDroppes = $tauxPresenceEtudiants->filter(fn($e) => $e['dropped'])->values();

        return [
            'tauxPresenceEtudiants' => collect($tauxPresenceEtudiants),
            'etudiantsDroppes' => collect($etudiantsDroppes),
            'seancesAujourdhui' => $seancesAujourdhui,
            'presencesASaisir' => $presencesASaisir,
            'absencesNonJustifiees' => $absencesNonJustifiees
        ];
    }

    public function statistiques(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->coordinateur) {
                throw new \Exception('Seul un coordinateur peut accéder aux statistiques.');
            }

            // Récupérer les IDs depuis la requête ou derniers par défaut
            $anneeId = $request->input('annee') ?? AnneeAcademique::latest()->first()->id;
            $semestreId = $request->input('semestre') ?? Semestre::latest()->first()->id;

            // Récupérer les objets complets pour accéder aux dates
            $anneeObj = AnneeAcademique::find($anneeId);
            $semestreObj = Semestre::find($semestreId);

            $annees = AnneeAcademique::all();
            $semestres = Semestre::orderBy('date_debut_semestre', 'desc')->get();

            // Initialiser les variables
            $tauxPresenceEtudiants = [];
            $tauxPresenceClasses = [];
            $volumeCoursParType = [];
            $volumeCumuleCours = [];

            // Calculer les données seulement si les filtres sont valides
            if ($anneeObj && $semestreObj) {
                $tauxPresenceEtudiants = $this->getTauxPresenceEtudiants($anneeObj, $semestreObj);
                $tauxPresenceClasses = $this->getTauxPresenceClasses($anneeObj, $semestreObj);
                $volumeCoursParType = $this->getVolumeCoursParType($anneeObj, $semestreObj);
                $volumeCumuleCours = $this->getVolumeCumuleCours($anneeObj, $semestreObj);
            }

            return view('Coordinateur.statistiques', compact(
                'annees',
                'semestres',
                'tauxPresenceEtudiants',
                'tauxPresenceClasses',
                'volumeCoursParType',
                'volumeCumuleCours'
            ));
        } catch (\Exception $e) {
            Log::error('Erreur lors du calcul des statistiques', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Une erreur est survenue lors du calcul des statistiques');
        }
    }


    private function getTauxPresenceEtudiants($anneeObj = null, $semestreObj = null)
    {
        $startDate = $semestreObj->date_debut_semestre ?? null;
        $endDate = $semestreObj->date_fin_semestre ?? null;

        $query = Etudiant::with('user')
            ->whereHas('presences');

        if ($startDate && $endDate) {
            $query->whereHas('presences.seance', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date_seance', [$startDate, $endDate]);
            });
        }

        if ($anneeObj) {
            $anneeId = $anneeObj->id;
            $query->whereHas('classes', function ($q) use ($anneeId) {
                $q->where('etudiant_classe.annee_academique_id', $anneeId);
            });
        }

        return $query->get()
            ->map(function ($etudiant) use ($startDate, $endDate) {
                $presencesQuery = Presence::where('etudiant_id', $etudiant->id);

                if ($startDate && $endDate) {
                    $presencesQuery->whereHas('seance', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date_seance', [$startDate, $endDate]);
                    });
                }

                $totalPresences = $presencesQuery->count();
                $absences = (clone $presencesQuery)->where('statut_presence_id', 2)->count();

                $taux = $totalPresences > 0
                    ? round(100 - ($absences / $totalPresences * 100), 1)
                    : 0;

                return [
                    'nom' => $etudiant->user->nom,
                    'taux' => $taux,
                    'couleur' => $this->getCouleurTaux($taux)
                ];
            })
            ->sortByDesc('taux')
            ->values();
    }


    private function getTauxPresenceClasses($anneeObj = null, $semestreObj = null)
    {
        $startDate = $semestreObj->date_debut_semestre ?? null;
        $endDate = $semestreObj->date_fin_semestre ?? null;
        $anneeId = $anneeObj->id ?? null;

        // Récupérer toutes les classes qui ont des étudiants liés à cette année académique
        $classeIds = DB::table('etudiant_classe')
            ->where('annee_academique_id', $anneeId)
            ->pluck('classe_id')
            ->unique();

        $classes = Classe::whereIn('id', $classeIds)->get()->map(function ($classe) use ($startDate, $endDate) {
            $query = Presence::whereHas('seance', function ($q) use ($classe, $startDate, $endDate) {
                $q->where('classe_id', $classe->id);
                if ($startDate && $endDate) {
                    $q->whereBetween('date_seance', [$startDate, $endDate]);
                }
            });

            $totalPresences = $query->count();
            $absences = (clone $query)->where('statut_presence_id', 2)->count();

            $taux = $totalPresences > 0
                ? round(100 - ($absences / $totalPresences * 100), 1)
                : 0;

            return [
                'classe' => $classe->nom_classe,
                'taux' => $taux
            ];
        });

        return $classes;
    }



    private function getVolumeCoursParType($anneeObj = null, $semestreObj = null)
    {
        $typesCours = TypeCours::all();
        $mois = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois[] = $date->format('Y-m');
        }

        $dateDebutSemestre = $semestreObj->date_debut_semestre ?? null;
        $dateFinSemestre = $semestreObj->date_fin_semestre ?? null;

        $anneeId = $anneeObj->id ?? null;

        $data = [];
        foreach ($typesCours as $type) {
            $volumes = [];
            foreach ($mois as $moisAnnee) {
                $query = Seance::where('type_cours_id', $type->id)
                    ->whereRaw("DATE_FORMAT(date_seance, '%Y-%m') = ?", [$moisAnnee]);

                if ($anneeId) {
                    $query->whereHas('classe.etudiants', function ($q) use ($anneeId) {
                        $q->where('etudiant_classe.annee_academique_id', $anneeId);
                    });
                }

                if ($dateDebutSemestre && $dateFinSemestre) {
                    $query->whereBetween('date_seance', [$dateDebutSemestre, $dateFinSemestre]);
                }

                $volumes[] = $query->count();
            }

            $data[] = [
                'label' => $type->nom_type_cours,
                'data' => $volumes,
                'backgroundColor' => $this->getColorForTypeCours($type->nom_type_cours)
            ];
        }

        return [
            'labels' => array_map(function ($m) {
                return date('M Y', strtotime($m . '-01'));
            }, $mois),
            'datasets' => $data
        ];
    }


    private function getVolumeCumuleCours($anneeObj = null, $semestreObj = null)
    {
        $now = now();

        $trimestres = [];
        for ($i = 3; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i * 3);
            $trimestre = ceil($date->month / 3);
            $year = $date->year;
            $trimestres[] = "T{$trimestre} {$year}";
        }

        $typesCours = TypeCours::all();
        $datasets = [];

        $dateDebutSemestre = $semestreObj->date_debut_semestre ?? null;
        $dateFinSemestre = $semestreObj->date_fin_semestre ?? null;
        $anneeId = $anneeObj->id ?? null;

        foreach ($typesCours as $type) {
            $data = [];
            for ($i = 3; $i >= 0; $i--) {
                $date = $now->copy()->subMonths($i * 3);
                $startMonth = ceil($date->month / 3) * 3 - 2;
                $endMonth = $startMonth + 2;

                $query = Seance::where('type_cours_id', $type->id)
                    ->whereYear('date_seance', $date->year)
                    ->whereMonth('date_seance', '>=', $startMonth)
                    ->whereMonth('date_seance', '<=', $endMonth);

                if ($anneeId) {
                    $query->whereHas('classe.etudiants', function ($q) use ($anneeId) {
                        $q->where('etudiant_classe.annee_academique_id', $anneeId);
                    });
                }

                if ($dateDebutSemestre && $dateFinSemestre) {
                    $query->whereBetween('date_seance', [$dateDebutSemestre, $dateFinSemestre]);
                }

                $data[] = $query->count();
            }

            $datasets[] = [
                'label' => $type->nom_type_cours,
                'data' => $data,
                'borderColor' => $this->getColorForTypeCours($type->nom_type_cours),
                'backgroundColor' => $this->getColorForTypeCours($type->nom_type_cours) . '20',
                'fill' => false
            ];
        }

        return [
            'labels' => $trimestres,
            'datasets' => $datasets
        ];
    }


    private function getColorForTypeCours($nomType)
    {
        $colors = [
            'présentiel' => '#3b82f6',    // Bleu
            'presentiel' => '#3b82f6',
            'e-learning' => '#fbbf24',    // Jaune
            'elearning' => '#fbbf24',
            'workshop' => '#f97316',      // Orange
            'atelier' => '#f97316',
        ];
        return $colors[strtolower($nomType)] ?? '#6b7280'; // Gris par défaut
    }

    private function getCouleurTaux($taux)
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
