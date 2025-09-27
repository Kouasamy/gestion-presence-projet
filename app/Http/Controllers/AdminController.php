<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnneeAcademique\StoreAnneeAcademiqueRequest;
use App\Http\Requests\AnneeAcademique\UpdateAnneeAcademiqueRequest;
use App\Http\Requests\Classe\StoreClasseRequest;
use App\Http\Requests\Classe\UpdateClasseRequest;
use App\Http\Requests\Etudiant\AssignerParentRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Requests\Semestre\StoreSemestreRequest;
use App\Http\Requests\Semestre\UpdateSemestreRequest;
use App\Http\Requests\Matiere\StoreMatiereRequest;
use App\Http\Requests\Matiere\UpdateMatiereRequest;
use App\Http\Requests\TypeCours\StoreTypeCoursRequest;
use App\Http\Requests\TypeCours\UpdateTypeCoursRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Role;
use App\Models\Semestre;
use App\Models\StatutPresence;
use App\Models\StatutSeance;
use App\Models\TypeCours;
use App\Models\User;
use App\Services\AnneeAcademiqueService;
use App\Services\ClasseService;
use App\Services\MatiereService;
use App\Services\TypeCoursService;
use App\Services\EtudiantService;
use App\Services\RoleService;
use App\Services\SemestreService;
use App\Services\UserService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $userService;

    protected $roleService;

    protected $anneeAcademiqueService;

    protected $semestreService;

    protected $etudiantService;

    protected $matiereService;

    protected $typeCoursService;

    protected $classeService;

    /**
     * Constructeur avec injection des dépendances
     */
    public function __construct(
        UserService $userService,
        RoleService $roleService,
        AnneeAcademiqueService $anneeAcademiqueService,
        SemestreService $semestreService,
        EtudiantService $etudiantService,
        MatiereService $matiereService,
        TypeCoursService $typeCoursService,
        ClasseService $classeService
    ) {
        $this->middleware('auth');
        $this->middleware('isAdmin');

        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->anneeAcademiqueService = $anneeAcademiqueService;
        $this->semestreService = $semestreService;
        $this->etudiantService = $etudiantService;
        $this->matiereService = $matiereService;
        $this->typeCoursService = $typeCoursService;
        $this->classeService = $classeService;
    }

    /**
     * Affiche le tableau de bord de l'administrateur
     */
    public function dashboard()
    {
        return view('Admin.dashboard');
    }

    /**
     * Liste les utilisateurs par rôle
     */
    public function indexUsersByRole($roleName = null)
    {
        $users = $this->userService->getUsersByRole($roleName);

        return view('admin.listeUsers', compact('users'));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function createUserForm()
    {
        $roles = Role::all();

        return view('Admin.formUser', compact('roles'));
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function storeUser(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        return redirect()->route('admin.user.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function editUserForm(User $user)
    {
        $roles = Role::all();

        return view('Admin.editUser', compact('user', 'roles'));
    }

    /**
     * Met à jour un utilisateur existant
     */
    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser($user, $request->validated());

        return redirect()->route('admin.user.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroyUser(User $user)
    {
        $this->userService->deleteUser($user);

        return redirect()->route('admin.user.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Liste les rôles
     */
    public function indexRoles()
    {
        $roles = $this->roleService->getAllRoles();

        return view('Admin.listeRole', compact('roles'));
    }

    /**
     * Affiche le formulaire de création d'un rôle
     */
    public function createRole()
    {
        return view('Admin.formRole');
    }

    /**
     * Crée un nouveau rôle
     */
    public function storeRole(StoreRoleRequest $request)
    {
        $this->roleService->createRole($request->validated());

        return redirect()->route('admin.role.index')->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un rôle
     */
    public function editRole(Role $role)
    {
        return view('Admin.editRole', compact('role'));
    }

    /**
     * Met à jour un rôle existant
     */
    public function updateRole(UpdateRoleRequest $request, Role $role)
    {
        $this->roleService->updateRole($role, $request->validated());

        return redirect()->route('admin.role.index')->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Supprime un rôle
     */
    public function destroyRole(Role $role)
    {
        $this->roleService->deleteRole($role);

        return redirect()->route('admin.role.index')->with('success', 'Rôle supprimé avec succès.');
    }


    /**
     * Liste les types de cours
     */
    public function listeTypesCours()
    {
        $typesCours = TypeCours::all();
        return view('Admin.listeTypesCours', compact('typesCours'));
    }

    /**
     * Affiche le formulaire de création d'un type de cours
     */
    public function createTypeCours()
    {
        return view('Admin.formTypeCours');
    }

    /**
     * Crée un nouveau type de cours
     */
    public function storeTypeCours(StoreTypeCoursRequest $request)
    {
        $this->typeCoursService->createTypeCours($request->validated());

        return redirect()->route('admin.types-cours.index')->with('success', 'Type de cours créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un type de cours
     */
    public function editTypeCours(TypeCours $type)
    {
        return view('Admin.editTypeCours', compact('type'));
    }

    /**
     * Met à jour un type de cours existant
     */
    public function updateTypeCours(UpdateTypeCoursRequest $request, TypeCours $type)
    {
        try {
            $this->typeCoursService->updateTypeCours($type, $request->validated());
            return redirect()->route('admin.types-cours.index')->with('success', 'Type de cours mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour du type de cours: '.$e->getMessage()]);
        }
    }

    /**
     * Supprime un type de cours
     */
    public function destroyTypeCours(TypeCours $type)
    {
        try {
            $this->typeCoursService->deleteTypeCours($type);
            return redirect()->route('admin.types-cours.index')->with('success', 'Type de cours supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('admin.types-cours.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Liste les classes
     */
    public function listeClasse()
    {
        $classes = $this->classeService->getAllClasses();

        return view('Admin.listeClasse', compact('classes'));
    }

    /**
     * Affiche le formulaire de création d'une classe
     */
    public function createClasse()
    {
        return view('Admin.formClasse');
    }

    /**
     * Crée une nouvelle classe
     */
    public function storeClasse(StoreClasseRequest $request)
    {
        $this->classeService->createClasse($request->validated());

        return redirect()->route('admin.classes.index')->with('success', 'Classe créée avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'une classe
     */
    public function editClasse(Classe $classe)
    {
        return view('Admin.editClasse', compact('classe'));
    }

    /**
     * Met à jour une classe existante
     */
    public function updateClasse(UpdateClasseRequest $request, Classe $classe)
    {
        $this->classeService->updateClasse($classe, $request->validated());

        return redirect()->route('admin.classes.index')->with('success', 'Classe mise à jour avec succès.');
    }

    /**
     * Supprime une classe
     */
    public function destroyClasse(Classe $classe)
    {
        $this->classeService->deleteClasse($classe);

        return redirect()->route('admin.classes.index')->with('success', 'Classe supprimée avec succès.');
    }

    /**
     * Liste les statuts de séance
     */
    public function listeStatutSeance()
    {
        $statuts = StatutSeance::all();

        return view('Admin.listeStatutSeance', compact('statuts'));
    }

    /**
     * Affiche le formulaire de création d'un statut de séance
     */
    public function createStatutSeance()
    {
        return view('Admin.formStatutSeance');
    }

    /**
     * Crée un nouveau statut de séance
     */
    public function storeStatutSeance(Request $request)
    {
        $request->validate([
            'nom_statut_seance' => 'required|string|max:255|unique:statut_seances,nom_statut_seance',
        ]);

        StatutSeance::create($request->only(['nom_statut_seance']));

        return redirect()->route('admin.statut-seances.index')->with('success', 'Statut de séance créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un statut de séance
     */
    public function editStatutSeance(StatutSeance $statut)
    {
        return view('Admin.editStatutSeance', compact('statut'));
    }

    /**
     * Met à jour un statut de séance existant
     */
    public function updateStatutSeance(Request $request, StatutSeance $statut)
    {
        $request->validate([
            'nom_statut_seance' => 'required|string|max:255|unique:statut_seances,nom_statut_seance,'.$statut->id,
        ]);

        $statut->update($request->only(['nom_statut_seance']));

        return redirect()->route('admin.statut-seances.index')->with('success', 'Statut de séance mis à jour avec succès.');
    }

    /**
     * Supprime un statut de séance
     */
    public function destroyStatutSeance(StatutSeance $statut)
    {
        $statut->delete();

        return redirect()->route('admin.statut-seances.index')->with('success', 'Statut de séance supprimé avec succès.');
    }

    /**
     * Liste les statuts de présence
     */
    public function indexStatutPresence()
    {
        $statutPresences = StatutPresence::all();

        return view('Admin.listeStatutPresence', compact('statutPresences'));
    }

    /**
     * Affiche le formulaire de création d'un statut de présence
     */
    public function createStatutPresence()
    {
        return view('Admin.formStatutPresence');
    }

    /**
     * Crée un nouveau statut de présence
     */
    public function storeStatutPresence(Request $request)
    {
        $request->validate([
            'nom_statut_presence' => 'required|string|max:255|unique:statut_presences,nom_statut_presence',
        ]);

        StatutPresence::create($request->only(['nom_statut_presence']));

        return redirect()->route('admin.statut-presences.index')->with('success', 'Statut de présence créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un statut de présence
     */
    public function editStatutPresence(StatutPresence $statut)
    {
        return view('Admin.editStatutPresence', compact('statut'));
    }

    /**
     * Met à jour un statut de présence existant
     */
    public function updateStatutPresence(Request $request, StatutPresence $statut)
    {
        $request->validate([
            'nom_statut_presence' => 'required|string|max:255|unique:statut_presences,nom_statut_presence,'.$statut->id,
        ]);

        $statut->update($request->only(['nom_statut_presence']));

        return redirect()->route('admin.statut-presences.index')->with('success', 'Statut de présence mis à jour avec succès.');
    }

    /**
     * Supprime un statut de présence
     */
    public function destroyStatutPresence(StatutPresence $statut)
    {
        $statut->delete();

        return redirect()->route('admin.statut-presences.index')->with('success', 'Statut de présence supprimé avec succès.');
    }

    /**
     * Affiche le formulaire d'assignation d'un parent à un étudiant
     */
    public function formAssignerParent(Etudiant $etudiant)
    {
        // Récupérer les utilisateurs avec le rôle parent
        $usersParents = $this->userService->getUsersByRole('parent');

        // Récupérer les objets Parents correspondants
        $parents = [];
        foreach ($usersParents as $user) {
            if ($user->parent) {
                $parents[] = [
                    'id' => $user->parent->id,
                    'nom' => $user->nom,
                    'email' => $user->email
                ];
            }
        }

        return view('Admin.formAssignerParent', compact('etudiant', 'parents'));
    }

    /**
     * Assigne un parent à un étudiant
     */
    public function assignerParent(AssignerParentRequest $request, Etudiant $etudiant)
    {
        $this->etudiantService->assignerParent($etudiant->id, $request->parent_id);

        return redirect()->route('admin.user.index', ['roleName' => 'etudiant'])
            ->with('success', 'Parent assigné avec succès.');
    }

    /**
     * Liste les années académiques
     */
    public function indexAnnee()
    {
        $annees = $this->anneeAcademiqueService->getAllAnnees();

        return view('Admin.listeAnneeAcademique', compact('annees'));
    }

    /**
     * Affiche le formulaire de création d'une année académique
     */
    public function createAnnee()
    {
        return view('Admin.formAnneeAcademique');
    }

    /**
     * Crée une nouvelle année académique
     */
    public function storeAnnee(StoreAnneeAcademiqueRequest $request)
    {
        $this->anneeAcademiqueService->createAnnee($request->validated());

        return redirect()->route('admin.annees.index')->with('success', 'Année créée.');
    }

    /**
     * Affiche le formulaire d'édition d'une année académique
     */
    public function editAnnee($id)
    {
        $annee = AnneeAcademique::findOrFail($id);

        return view('Admin.editAnneeAcademique', compact('annee'));
    }

    /**
     * Met à jour une année académique existante
     */
    public function updateAnnee(UpdateAnneeAcademiqueRequest $request, $id)
    {
        $this->anneeAcademiqueService->updateAnnee($id, $request->validated());

        return redirect()->route('admin.annees.index')->with('success', 'Année mise à jour.');
    }

    /**
     * Supprime une année académique
     */
    public function destroyAnnee($id)
    {
        $this->anneeAcademiqueService->deleteAnnee($id);

        return redirect()->route('admin.annees.index')->with('success', 'Année supprimée.');
    }

    /**
     * Liste les semestres
     */
    public function indexSemestre()
    {
        $semestres = $this->semestreService->getAllSemestres();

        return view('Admin.listeSemestres', compact('semestres'));
    }

    /**
     * Affiche le formulaire de création d'un semestre
     */
    public function createSemestre()
    {
        $annees = AnneeAcademique::all();

        return view('Admin.formSemestres', compact('annees'));
    }

    /**
     * Crée un nouveau semestre
     */
    public function storeSemestre(StoreSemestreRequest $request)
    {
        $this->semestreService->createSemestre($request->validated());

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre créé avec succès.');
    }

    /**
     * Affiche le formulaire d'édition d'un semestre
     */
    public function editSemestre($id)
    {
        $semestre = Semestre::findOrFail($id);
        $annees = AnneeAcademique::all();

        return view('Admin.editSemestres', compact('semestre', 'annees'));
    }

    /**
     * Met à jour un semestre existant
     */
    public function updateSemestre(UpdateSemestreRequest $request, $id)
    {
        $this->semestreService->updateSemestre($id, $request->validated());

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre mis à jour avec succès.');
    }

    /**
     * Supprime un semestre
     */
    public function destroySemestre($id)
    {
        $this->semestreService->deleteSemestre($id);

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre supprimé avec succès.');
    }

    /**
     * Liste les matières
     */
    public function listeMatieres()
    {
        $matieres = $this->matiereService->getAllMatieres();
        return view('Admin.listeMatieres', compact('matieres'));
    }

    /**
     * Affiche le formulaire de création d'une matière
     */
    public function createMatiere()
    {
        return view('Admin.formMatiere');
    }

    /**
     * Crée une nouvelle matière
     */
    public function storeMatiere(StoreMatiereRequest $request)
    {
        try {
            $this->matiereService->createMatiere($request->validated());

            return redirect()->route('admin.matieres.index')->with('success', 'Matière créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Erreur lors de la création de la matière: '.$e->getMessage()]);
        }
    }

    /**
     * Affiche le formulaire d'édition d'une matière
     */
    public function editMatiere(Matiere $matiere)
    {
        return view('Admin.editMatiere', compact('matiere'));
    }

    /**
     * Met à jour une matière existante
     */
    public function updateMatiere(UpdateMatiereRequest $request, Matiere $matiere)
    {
        try {
            $this->matiereService->updateMatiere($matiere, $request->validated());

            return redirect()->route('admin.matieres.index')->with('success', 'Matière mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('admin.matieres.index')->withErrors(['error' => 'Erreur lors de la mise à jour de la matière: '.$e->getMessage()]);
        }
    }

    /**
     * Supprime une matière
     */
    public function destroyMatiere(Matiere $matiere)
    {
        try {
            $this->matiereService->deleteMatiere($matiere);

            return redirect()->route('admin.matieres.index')->with('success', 'Matière supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('admin.matieres.index')->withErrors(['error' => 'Erreur lors de la suppression de la matière: '.$e->getMessage()]);
        }
    }
}
