<?php

namespace App\Http\Controllers;

use App\Services\EtudiantService;
use Illuminate\Support\Facades\Auth;

class EtudiantController extends Controller
{
    protected $etudiantService;

    /**
     * Constructeur avec injection des dépendances
     */
    public function __construct(EtudiantService $etudiantService)
    {
        $this->middleware(['auth', 'isEtudiant']);
        $this->etudiantService = $etudiantService;
    }

    /**
     * Affiche le tableau de bord de l'étudiant
     */
    public function dashboard()
    {
        $etudiantId = Auth::user()->etudiant->id;
        $statistiques = $this->etudiantService->getStatistiquesEtudiant($etudiantId);

        return view('Etudiant.dashboardEtudiant', [
            'tauxGlobal' => $statistiques['tauxGlobal'],
            'absencesNonJustifiees' => $statistiques['absencesNonJustifiees'],
            'presencesParMatiere' => $statistiques['presencesParMatiere'],
            'assiduite' => $statistiques['assiduite'],
            'droppedSubjects' => $statistiques['droppedSubjects'],
        ]);
    }

    /**
     * Affiche l'emploi du temps de l'étudiant connecté
     */
    public function emploiDuTemps()
    {
        $etudiantId = Auth::user()->etudiant->id;
        $data = $this->etudiantService->getEmploiDuTemps($etudiantId);

        return view('Etudiant.emploiDuTemps', $data);
    }

    /**
     * Affiche les absences de l'étudiant connecté
     */
    public function listeAbsences()
    {
        $etudiantId = Auth::user()->etudiant->id;
        $absences = $this->etudiantService->getAbsences($etudiantId);

        return view('Etudiant.absences', compact('absences'));
    }

    /**
     * Affiche la note d'assiduité de l'étudiant
     */
    public function noteAssiduite()
    {
        $etudiantId = Auth::user()->etudiant->id;
        $assiduite = $this->etudiantService->getNoteAssiduite($etudiantId);

        return view('Etudiant.noteAssiduite', compact('assiduite'));
    }
}
