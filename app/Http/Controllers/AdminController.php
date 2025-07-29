<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Parents;
use App\Models\Role;
use App\Models\Semestre;
use App\Models\StatutPresence;
use App\Models\StatutSeance;
use App\Models\TypeCours;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

   public function indexUsersByRole($roleName = null)
{
    if ($roleName) {
        $users = User::whereHas('role', function ($query) use ($roleName) {
            $query->where('nom_role', $roleName);
        })->get();
    } else {
        $users = User::all();
    }

    return view("admin.listeUsers", compact('users'));
}


    public function createUserForm()
    {
        $roles = Role::all();
        return view('admin.formUser', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'photo_path' => 'nullable|image|max:2048',
            'role_id' => 'required|exists:roles,id',
        ]);
        // Récupération du rôle
        $role = Role::find($request->input('role_id'));

        if (!$role) {
            return redirect()->back()->withErrors(['role_id' => 'Rôle invalide.']);
        }
        // Gestion de la photo
        $photoPath = null;
        if ($request->hasFile('photo_path')) {
            $photoPath = $request->file('photo_path')->store('photos', 'public');
        }

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $request->input('nom'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'photo_path' => $photoPath,
            'role_id' => $request->input('role_id'),
        ]);

        // Association du rôle à l'utilisateur
        $roleName = trim(strtolower($role->nom_role));

        switch ($roleName) {
            case 'parent':
                Parents::create(['user_id' => $user->id]);
                break;

            case 'enseignant':
                Enseignant::create(['user_id' => $user->id]);
                break;

            case 'coordinateur':
                Coordinateur::create(['user_id' => $user->id]);
                break;

            case 'etudiant':
                Etudiant::create(['user_id' => $user->id, 'photo_path' => $photoPath]);
                break;
        }








        // Message par défaut
        $message = "Utilisateur créé avec succès.";


        return redirect()->back()->with('success', $message);
    }
     public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function editUserForm(User $user)
    {
        $roles = Role::all();
        return view('admin.editUser', compact('user', 'roles'));
    }

    public function indexRoles()
    {
        $roles = Role::all();
        return view('admin.listeRole', compact('roles'));
    }

    // Formulaire de création de rôle
    public function createRole()
    {
        return view('admin.formRole');
    }

    // Enregistre un nouveau rôle
    public function storeRole(Request $request)
    {
        $request->validate([
            'nom_role' => 'required|string|unique:roles,nom_role',
        ]);

        Role::create([
            'nom_role' => $request->nom_role,
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    // Formulaire de modification d’un rôle
    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.formRole', compact('role'));
    }

    // Met à jour un rôle
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'nom_role' => 'required|string|unique:roles,nom_role,' . $id,
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'nom_role' => $request->nom_role,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Rôle modifié avec succès.');
    }

    // Supprime un rôle
    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Rôle supprimé avec succès.');
    }

    public function listeCours()
    {
        $matieres = Matiere::all();
        $typesCours = TypeCours::all();

        return view('admin.listeCours', compact('matieres', 'typesCours'));
    }

    public function indexCours()
    {
        return view('admin.formCours');
    }

    public function storeCours(Request $request)
    {
        $request->validate([
            'nom_matiere' => 'required|string|max:255|unique:matieres,nom_matiere'
        ]);

        Matiere::create([
            'nom_matiere' => $request->nom_matiere
        ]);

        return redirect()->route('admin.cours.index')
            ->with('success', 'Cours ajouté avec succès.');
    }
    public function storeTypeCours(Request $request)
    {
        $request->validate([
            'nom_type_cours' => 'required|string|max:255|unique:type_cours,nom_type_cours'
        ]);

        TypeCours::create([
            'nom_type_cours' => $request->nom_type_cours
        ]);

        return redirect()->route('admin.cours.index')
            ->with('success', 'Type de cours ajouté avec succès.');
    }
    public function editCours($id)
    {
        $matiere = Matiere::findOrFail($id);
        return view('admin.editCoursType', compact('matiere'));
    }

    public function updateCours(Request $request, $id)
    {
        $request->validate([
            'nom_matiere' => 'required|string|max:255|unique:matieres,nom_matiere,' . $id
        ]);

        $matiere = Matiere::findOrFail($id);
        $matiere->update([
            'nom_matiere' => $request->nom_matiere
        ]);

        // Correction du nom de la route
        return redirect()->route('admin.cours.index')->with('success', 'Cours modifié.');
    }

    public function editTypeCours($id)
    {
        $type = TypeCours::findOrFail($id);
        return view('admin.editCoursType', compact('type'));
    }

    public function updateTypeCours(Request $request, $id)
    {
        $request->validate([
            'nom_type_cours' => 'required|string|max:255|unique:type_cours,nom_type_cours,' . $id
        ]);

        $type = TypeCours::findOrFail($id);
        $type->update([
            'nom_type_cours' => $request->nom_type_cours
        ]);

        // Correction du nom de la route
        return redirect()->route('admin.cours.index')->with('success', 'Type de cours modifié.');
    }
    public function destroyCours($id)
    {
        $matiere = Matiere::findOrFail($id);
        $matiere->delete();

        return back()->with('success', 'Cours supprimé.');
    }
    public function destroyTypeCours($id)
    {
        $type = TypeCours::findOrFail($id);
        $type->delete();

        return back()->with('success', 'Type de cours supprimé.');
    }

    public function listeClasse()
    {
        $classes = Classe::all();
        return view('admin.listeClasse', compact('classes'));
    }


    public function createClasse()
    {
        return view('admin.formClasse');
    }

    public function storeClasse(Request $request)
    {
        $request->validate([
            'nom_classe' => 'required|string|max:255|unique:classes,nom_classe',
        ]);

        Classe::create([
            'nom_classe' => $request->nom_classe,
        ]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Classe ajoutée avec succès.');
    }


    public function editClasse($id)
    {
        $classe = Classe::findOrFail($id);
        return view('admin.editClasse', compact('classe'));
    }


    public function updateClasse(Request $request, $id)
    {
        $request->validate([
            'nom_classe' => 'required|string|max:255|unique:classes,nom_classe,' . $id,
        ]);

        $classe = Classe::findOrFail($id);
        $classe->update([
            'nom_classe' => $request->nom_classe,
        ]);

        return redirect()->route('classe.liste')->with('success', 'Classe mise à jour.');
    }


public function destroyClasse($id)
{
    $classe = Classe::findOrFail($id);
    $classe->delete();

    return redirect()->route('classe.liste')->with('success', 'Classe supprimée.');
}

    /**
        * Affiche le formulaire pour assigner un parent à un étudiant.
     */
    public function formAssignerParent(Etudiant $etudiant)
    {
        $parents = User::whereHas('role', function ($query) {
            $query->where('nom_role', 'parent');
        })->get();

        return view('admin.formAssignerParent', compact('etudiant', 'parents'));
    }

    /**
     * Assigne un parent à un étudiant.
     */
    public function assignerParent(Request $request, Etudiant $etudiant)
    {
        $request->validate([
            'parent_id' => 'required|exists:users,id',
        ]);

        $parentUser = User::find($request->parent_id);

        if ($parentUser && $parentUser->parent) {
            $etudiant->parents()->syncWithoutDetaching([$parentUser->parent->id]);
        } else {
            return redirect()->back()->with('error', 'Le parent sélectionné n\'est pas valide.');
        }

        return redirect()->route('admin.user.index')->with('success', 'Parent assigné avec succès.');
    }

    // CRUD pour les statuts de séance
    public function listeStatutSeance()
    {
        $statuts = StatutSeance::all();
        return view('admin.listeStatutSeance', compact('statuts'));
    }


    public function createStatutSeance()
    {
        return view('admin.formStatutSeance');
    }


    public function storeStatutSeance(Request $request)
    {
        $request->validate([
            'nom_seance' => 'required|string|max:255|unique:statut_seances,nom_seance',
        ]);

        StatutSeance::create([
            'nom_seance' => $request->nom_seance,
        ]);

        return redirect()->route('admin.statut-seances.index')
            ->with('success', 'Statut de séance ajouté avec succès.');
    }


    public function editStatutSeance($id)
    {
        $statut = StatutSeance::findOrFail($id);
        return view('admin.editStatutSeance', compact('statut'));
    }


    public function updateStatutSeance(Request $request, $id)
    {
        $request->validate([
            'nom_seance' => 'required|string|max:255|unique:statut_seances,nom_seance,' . $id,
        ]);

        $statut = StatutSeance::findOrFail($id);
        $statut->update([
            'nom_seance' => $request->nom_seance,
        ]);

        return redirect()->route('statutseance.liste')->with('success', 'Statut de séance mis à jour.');
    }


    public function destroyStatutSeance($id)
    {
        $statut = StatutSeance::findOrFail($id);
        $statut->delete();

        return redirect()->route('statutseance.liste')->with('success', 'Statut de séance supprimé.');
    }

    public function indexStatutPresence()
    {
        $statutPresences = StatutPresence::all();
        return view('admin.listeStatutPresence', compact('statutPresences'));
    }


    public function createStatutPresence()
    {
        return view('admin.formStatutPresence');
    }


    public function storeStatutPresence(Request $request)
    {
        $request->validate([
            'nom_statut_presence' => 'required|string|max:255',
        ]);

        StatutPresence::create([
            'nom_statut_presence' => $request->nom_statut_presence,
        ]);

        return redirect()->route('admin.statut-presences.index')
            ->with('success', 'Statut présence ajouté.');
    }


    public function editStatutPresence($id)
    {
        $statutPresence = StatutPresence::findOrFail($id);
        return view('admin.editStatutPresence', compact('statutPresence'));
    }


    public function updateStatutPresence(Request $request, $id)
    {
        $request->validate([
            'nom_statut_presence' => 'required|string|max:255',
        ]);

        $statutPresence = StatutPresence::findOrFail($id);
        $statutPresence->update([
            'nom_statut_presence' => $request->nom_statut_presence,
        ]);

        // Correction du nom de la route
        return redirect()->route('admin.statut-presences.index')->with('success', 'Statut présence modifié.');
    }


    public function destroyStatutPresence($id)
    {
        StatutPresence::findOrFail($id)->delete();
        return redirect()->route('admin.statut-presence.index')->with('success', 'Statut présence supprimé.');
    }

    //CRUD pour les années académiques
   public function indexAnnee()
{
    $annees = AnneeAcademique::withCount('semestres')->get();
    return view('admin.listeAnneeAcademique', compact('annees'));
}

public function createAnnee()
{
    return view('admin.formAnneeAcademique');
}

public function storeAnnee(Request $request)
{
    $request->validate(['annee' => 'required|string']);
    AnneeAcademique::create($request->only('annee'));

    return redirect()->route('admin.annees.create')->with('success', 'Année créée.');
}

public function editAnnee($id)
{
    $annee = AnneeAcademique::findOrFail($id);
    return view('admin.editAnneeAcademique', compact('annee'));
}

public function updateAnnee(Request $request, $id)
{
    $request->validate(['annee' => 'required|string']);
    $annee = AnneeAcademique::findOrFail($id);
    $annee->update($request->only('annee'));

    return redirect()->route('admin.annees.index')->with('success', 'Année mise à jour.');
}

public function destroyAnnee($id)
{
    $annee = AnneeAcademique::findOrFail($id);
    $annee->delete();

    return redirect()->route('admin.annees.index')->with('success', 'Année supprimée.');
}





    //CRUD pour les semestres
    // Affiche la liste des semestres
    public function indexSemestre()
{
    $semestres = Semestre::with('anneeAcademique')->get();
    return view('admin.listeSemestres', compact('semestres'));
}

public function createSemestre()
{
    $annees = AnneeAcademique::all();
    return view('admin.formSemestres', compact('annees'));
}

public function storeSemestre(Request $request)
{
    $request->validate([
        'nom' => 'required|string',
        'date_debut_semestre' => 'required|date',
        'date_fin_semestre' => 'required|date|after_or_equal:date_debut_semestre',
        'annees_academiques_id' => 'required|exists:annees_academiques,id',
    ]);

    Semestre::create($request->all());

    return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre créé avec succès.');
}

public function editSemestre($id)
{
    $semestre = Semestre::findOrFail($id);
    $annees = AnneeAcademique::all();
    return view('admin.editSemestres', compact('semestre', 'annees'));
}

public function updateSemestre(Request $request, $id)
{
    $request->validate([
        'nom' => 'required|string',
        'date_debut_semestre' => 'required|date',
        'date_fin_semestre' => 'required|date|after_or_equal:date_debut_semestre',
        'annees_academiques_id' => 'required|exists:annees_academiques,id',
    ]);

    $semestre = Semestre::findOrFail($id);
    $semestre->update($request->all());

    return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre mis à jour avec succès.');
}

public function destroySemestre($id)
{
    $semestre = Semestre::findOrFail($id);
    $semestre->delete();

    return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre supprimé avec succès.');
}

}
