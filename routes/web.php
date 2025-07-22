<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


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
// Route pour les admins
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Vue dynamique selon rôle
    //Gestion des utilisateurs par rôle
    Route::get('/{role}/liste', [AdminController::class, 'indexUsersByRole'])->name('role.liste');

    // Formulaire générique
    Route::get('/utilisateur/create', [AdminController::class, 'createUserForm'])->name('user.create');
    Route::post('/utilisateur/store', [AdminController::class, 'storeUser'])->name('user.store');

    // Gestion des rôles
    Route::get('/roles', [AdminController::class, 'indexRoles'])->name('roles.index');
    Route::get('/roles/create', [AdminController::class, 'createRole'])->name('roles.create');
    Route::post('/roles', [AdminController::class, 'storeRole'])->name('roles.store');
    Route::get('/roles/{id}/edit', [AdminController::class, 'editRole'])->name('roles.edit');
    Route::put('/roles/{id}', [AdminController::class, 'updateRole'])->name('roles.update');
    Route::delete('/roles/{id}', [AdminController::class, 'destroyRole'])->name('roles.destroy');

    // Cours (matières)
    Route::get('/cours', [AdminController::class, 'indexCours'])->name('cours.index'); // Formulaire
    Route::post('/cours/store', [AdminController::class, 'storeCours'])->name('cours.store');
    Route::get('/cours/liste', [AdminController::class, 'listeCours'])->name('cours.liste');
    Route::get('/cours/{id}/edit', [AdminController::class, 'editCours'])->name('cours.edit');
    Route::put('/cours/{id}', [AdminController::class, 'updateCours'])->name('cours.update');
    Route::delete('/cours/{id}', [AdminController::class, 'destroyCours'])->name('cours.destroy');

    // Type de cours
    Route::post('/type-cours/store', [AdminController::class, 'storeTypeCours'])->name('typecours.store');
    Route::get('/type-cours/{id}/edit', [AdminController::class, 'editTypeCours'])->name('typecours.edit');
    Route::put('/type-cours/{id}', [AdminController::class, 'updateTypeCours'])->name('typecours.update');
    Route::delete('/type-cours/{id}', [AdminController::class, 'destroyTypeCours'])->name('typecours.destroy');

    //Classes
    Route::get('/classes', [AdminController::class, 'listeClasse'])->name('classe.liste');
    Route::get('/classes/create', [AdminController::class, 'createClasse'])->name('classe.create');
    Route::post('/classes', [AdminController::class, 'storeClasse'])->name('classe.store');
    Route::get('/classes/{id}/edit', [AdminController::class, 'editClasse'])->name('classe.edit');
    Route::put('/classes/{id}', [AdminController::class, 'updateClasse'])->name('classe.update');
    Route::delete('/classes/{id}', [AdminController::class, 'destroyClasse'])->name('classe.destroy');

    // Statut Présence
    Route::get('/statutpresence', [AdminController::class, 'listeStatutPresence'])->name('statutpresence.liste');
    Route::get('/statutpresence/create', [AdminController::class, 'createStatutPresence'])->name('statutpresence.create');
    Route::post('/statutpresence', [AdminController::class, 'storeStatutPresence'])->name('statutpresence.store');
    Route::get('/statutpresence/{id}/edit', [AdminController::class, 'editStatutPresence'])->name('statutpresence.edit');
    Route::put('/statutpresence/{id}', [AdminController::class, 'updateStatutPresence'])->name('statutpresence.update');
    Route::delete('/statutpresence/{id}', [AdminController::class, 'destroyStatutPresence'])->name('statutpresence.destroy');
});



// Route pour les étudiants
Route::middleware(['auth', 'isEtudiant'])->group(function () {
    Route::get('/etudiant/dashboard', function () {
        return view('etudiant.dashboardEtudiant');
    })->name('etudiant.dashboard');
});




// Route pour les parents
Route::middleware(['auth', 'isParent'])->group(function () {
    Route::get('/parent/dashboard', function () {
        return view('parent.dashboard');
    })->name('parent.dashboard');
});



// Route pour les coordinateurs
Route::middleware(['auth', 'isCoordinateur'])->group(function () {
    Route::get('/coordinateur/dashboard', function () {
        return view('coordinateur.dashboardCoordinateur');
    })->name('coordinateur.dashboard');
});


// Route pour les enseignants
Route::middleware(['auth', 'isEnseignant'])->group(function () {
    Route::get('/enseignant/dashboard', function () {
        return view('enseignant.dashboardEnseignant');
    })->name('enseignant.dashboard');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
