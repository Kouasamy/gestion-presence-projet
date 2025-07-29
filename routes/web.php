<?php

use App\Http\Controllers\CoordinateurController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route d'accueil avec redirection selon le rôle
Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role->nom_role ?? null;
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'etudiant' => redirect()->route('etudiant.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            'coordinateur' => redirect()->route('coordinateur.dashboard'),
            'enseignant' => redirect()->route('enseignant.dashboard'),
            default => redirect('/login'),
        };
    }
    return redirect()->route('login');
});

// Routes pour l'administrateur
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Assignation parent - étudiant
    Route::get('/etudiants/{etudiant}/assigner-parent', [AdminController::class, 'formAssignerParent'])->name('etudiants.formAssignerParent');
    Route::post('/etudiants/{etudiant}/assigner-parent', [AdminController::class, 'assignerParent'])->name('etudiants.assignerParent');

    // Gestion des utilisateurs
    Route::prefix('users')->name('user.')->group(function () {
        Route::get('/', [AdminController::class, 'indexUsersByRole'])->name('index');
        Route::get('/create', [AdminController::class, 'createUserForm'])->name('create');
        Route::post('/', [AdminController::class, 'storeUser'])->name('store');
        Route::get('/{user}/edit', [AdminController::class, 'editUserForm'])->name('edit');
        Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('destroy');
    });

    // Gestion des rôles
    Route::prefix('roles')->name('role.')->group(function () {
        Route::get('/', [AdminController::class, 'indexRoles'])->name('index');
        Route::get('/create', [AdminController::class, 'createRole'])->name('create');
        Route::post('/', [AdminController::class, 'storeRole'])->name('store');
        Route::get('/{role}/edit', [AdminController::class, 'editRole'])->name('edit');
        Route::put('/{role}', [AdminController::class, 'updateRole'])->name('update');
        Route::delete('/{role}', [AdminController::class, 'destroyRole'])->name('destroy');
    });

    // Gestion des cours et types de cours
    Route::prefix('cours')->name('cours.')->group(function () {
        Route::get('/', [AdminController::class, 'listeCours'])->name('index');
        Route::get('/create', [AdminController::class, 'indexCours'])->name('create');
        Route::post('/', [AdminController::class, 'storeCours'])->name('store');
        Route::get('/{cours}/edit', [AdminController::class, 'editCours'])->name('edit');
        Route::put('/{cours}', [AdminController::class, 'updateCours'])->name('update');
        Route::delete('/{cours}', [AdminController::class, 'destroyCours'])->name('destroy');

        // Types de cours
        Route::prefix('types')->name('types.')->group(function () {
            Route::post('/', [AdminController::class, 'storeTypeCours'])->name('store');
            Route::get('/{type}/edit', [AdminController::class, 'editTypeCours'])->name('edit');
            Route::put('/{type}', [AdminController::class, 'updateTypeCours'])->name('update');
            Route::delete('/{type}', [AdminController::class, 'destroyTypeCours'])->name('destroy');
        });
    });

    // Gestion des classes
    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/', [AdminController::class, 'listeClasse'])->name('index');
        Route::get('/create', [AdminController::class, 'createClasse'])->name('create');
        Route::post('/', [AdminController::class, 'storeClasse'])->name('store');
        Route::get('/{classe}/edit', [AdminController::class, 'editClasse'])->name('edit');
        Route::put('/{classe}', [AdminController::class, 'updateClasse'])->name('update');
        Route::delete('/{classe}', [AdminController::class, 'destroyClasse'])->name('destroy');
    });

    // Gestion des statuts de séance
    Route::prefix('statut-seances')->name('statut-seances.')->group(function () {
        Route::get('/', [AdminController::class, 'listeStatutSeance'])->name('index');
        Route::get('/create', [AdminController::class, 'createStatutSeance'])->name('create');
        Route::post('/', [AdminController::class, 'storeStatutSeance'])->name('store');
        Route::get('/{statut}/edit', [AdminController::class, 'editStatutSeance'])->name('edit');
        Route::put('/{statut}', [AdminController::class, 'updateStatutSeance'])->name('update');
        Route::delete('/{statut}', [AdminController::class, 'destroyStatutSeance'])->name('destroy');
    });

    // Gestion des statuts de présence
    Route::prefix('statut-presences')->name('statut-presences.')->group(function () {
        Route::get('/', [AdminController::class, 'indexStatutPresence'])->name('index');
        Route::get('/create', [AdminController::class, 'createStatutPresence'])->name('create');
        Route::post('/', [AdminController::class, 'storeStatutPresence'])->name('store');
        Route::get('/{statut}/edit', [AdminController::class, 'editStatutPresence'])->name('edit');
        Route::put('/{statut}', [AdminController::class, 'updateStatutPresence'])->name('update');
        Route::delete('/{statut}', [AdminController::class, 'destroyStatutPresence'])->name('destroy');
    });

    // Gestion des années académiques
    Route::prefix('annees-academiques')->name('annees.')->group(function () {
        Route::get('/', [AdminController::class, 'indexAnnee'])->name('index');
        Route::get('/create', [AdminController::class, 'createAnnee'])->name('create');
        Route::post('/', [AdminController::class, 'storeAnnee'])->name('store');
        Route::get('/{annee}/edit', [AdminController::class, 'editAnnee'])->name('edit');
        Route::put('/{annee}', [AdminController::class, 'updateAnnee'])->name('update');
        Route::delete('/{annee}', [AdminController::class, 'destroyAnnee'])->name('destroy');
    });

    // Gestion des semestres
    Route::prefix('semestres')->name('semestres.')->group(function () {
        Route::get('/', [AdminController::class, 'indexSemestre'])->name('index');
        Route::get('/create', [AdminController::class, 'createSemestre'])->name('create');
        Route::post('/', [AdminController::class, 'storeSemestre'])->name('store');
        Route::get('/{semestre}/edit', [AdminController::class, 'editSemestre'])->name('edit');
        Route::put('/{semestre}', [AdminController::class, 'updateSemestre'])->name('update');
        Route::delete('/{semestre}', [AdminController::class, 'destroySemestre'])->name('destroy');
    });
});

// Routes pour le coordinateur
Route::middleware(['auth', 'isCoordinateur'])->prefix('coordinateur')->name('coordinateur.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CoordinateurController::class, 'dashboard'])->name('dashboard');

    // Gestion des séances
    Route::prefix('seances')->name('seances.')->group(function () {
        Route::get('/', [CoordinateurController::class, 'index'])->name('index');
        Route::get('/{seance}/edit', [CoordinateurController::class, 'edit'])->name('edit');
        Route::put('/{seance}', [CoordinateurController::class, 'update'])->name('update');
        Route::delete('/{seance}', [CoordinateurController::class, 'destroy'])->name('destroy');

        // Gestion des reports et annulations
        Route::post('/{seance}/reporter', [CoordinateurController::class, 'reporterSeance'])->name('reporter');
        Route::post('/{seance}/annuler', [CoordinateurController::class, 'annulerSeance'])->name('annuler');
    });
    // Statistiques
    Route::get('/statistiques', [CoordinateurController::class, 'statistiques'])->name('statistiques');


    // Gestion des présences
    Route::prefix('presences')->name('presences.')->group(function () {
        Route::get('/', [CoordinateurController::class, 'indexPresences'])->name('index');
        Route::get('/seance/{seance}', [CoordinateurController::class, 'presenceForm'])->name('form');
        Route::post('/seance/{seance}', [CoordinateurController::class, 'storePresence'])->name('store');
    });

    // Gestion des absences et justifications
    Route::prefix('absences')->name('absences.')->group(function () {
        Route::get('/', [CoordinateurController::class, 'indexAbsences'])->name('index');
        Route::get('/{absence}/details', [CoordinateurController::class, 'showJustificationDetails'])->name('details');
        Route::post('/{absence}/justify', [CoordinateurController::class, 'justifyAbsence'])->name('justify');
    });

    // Gestion des justifications
    Route::prefix('justifications')->name('justification.')->group(function () {
        Route::get('/create/{presence}', [CoordinateurController::class, 'createJustification'])->name('create');
        Route::post('/store/{presence}', [CoordinateurController::class, 'storeJustification'])->name('store');
        Route::get('/{absence}/details', [CoordinateurController::class, 'showJustificationDetails'])->name('details');
    });

    // Gestion de l'emploi du temps
    Route::prefix('emploi-du-temps')->name('emploiDuTemps.')->group(function () {
        Route::get('/', [CoordinateurController::class, 'indexEmploiDuTemps'])->name('index');
        Route::get('/create', [CoordinateurController::class, 'createEmploiDuTemps'])->name('create');
        Route::post('/', [CoordinateurController::class, 'storeEmploiDuTemps'])->name('store');
        Route::get('/{emploi}/edit', [CoordinateurController::class, 'editEmploiDuTemps'])->name('edit');
        Route::put('/{emploi}', [CoordinateurController::class, 'updateEmploiDuTemps'])->name('update');
        Route::get('/{classe}', [CoordinateurController::class, 'emploiDuTempsParClasse'])->name('show');
    });

    // Liste des étudiants
    Route::get('/etudiants', [CoordinateurController::class, 'listeEtudiants'])->name('etudiants.index');
    // Nouvelle route pour afficher le formulaire d'assignation
    Route::get('/etudiants/{etudiant}/assigner-classe', [CoordinateurController::class, 'formAssignerClasse'])->name('etudiants.formAssignerClasse');
    // Route POST existante pour traiter l'assignation
    Route::post('/etudiants/{etudiant}/assigner-classe', [CoordinateurController::class, 'assignerClasse'])->name('etudiants.assignerClasse');
    Route::post('/etudiants/{etudiant}/desinscrire-classe/{classe}', [CoordinateurController::class, 'desinscrireClasse'])->name('etudiants.desinscrireClasse');
});

// Routes pour les étudiants
Route::middleware(['auth', 'isEtudiant'])->prefix('etudiant')->name('etudiant.')->group(function () {
    Route::get('/dashboard', [EtudiantController::class, 'dashboard'])->name('dashboard');
    Route::get('/emploi-du-temps', [EtudiantController::class, 'emploiDuTemps'])->name('emploi');
    Route::get('/absences', [EtudiantController::class, 'listeAbsences'])->name('absences');
    Route::get('/assiduite', [EtudiantController::class, 'noteAssiduite'])->name('assiduite');
});

// Routes pour les parents


Route::middleware(['auth', 'isParent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
    Route::get('/emploi-du-temps', [ParentController::class, 'emploiDuTemps'])->name('emploiDuTemps');
    Route::get('/absences', [ParentController::class, 'absences'])->name('absences');
});



// Routes pour les enseignants
Route::middleware(['auth', 'isEnseignant'])->prefix('enseignant')->name('enseignant.')->group(function () {
    Route::get('/dashboard', function () {
        return view('enseignant.dashboardEnseignant');
    })->name('dashboard');

    Route::get('/seances', [EnseignantController::class, 'listeSeances'])->name('listeSeances');
    Route::get('/seances/{seance}/presence', [EnseignantController::class, 'formulairePresence'])->name('formulairePresence');
    Route::post('/seances/{seance}/presence', [EnseignantController::class, 'enregistrerPresence'])->name('enregistrerPresence');
    Route::get('/emploi-du-temps', [EnseignantController::class, 'emploiDuTemps'])->name('emploiDuTemps');
});

// Routes du profil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
