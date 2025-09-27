<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\Seance;
use App\Models\StatutPresence;
use App\Repositories\SeanceRepository;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EnseignantService
{
    protected $seanceRepository;
    protected $presenceService;
    protected $statistiqueService;

    /**
     * Constructeur avec injection des dépendances
     */
    public function __construct(
        SeanceRepository $seanceRepository,
        PresenceService $presenceService,
        StatistiqueService $statistiqueService
    ) {
        $this->seanceRepository = $seanceRepository;
        $this->presenceService = $presenceService;
        $this->statistiqueService = $statistiqueService;
    }

    /**
     * Récupérer la liste des séances pour un enseignant
     */
    public function getSeances(int $enseignantId, array $filters = []): LengthAwarePaginator
    {
        return $this->seanceRepository->getAllForEnseignant($enseignantId, $filters);
    }

    /**
     * Récupérer une séance avec les étudiants et les présences
     */
    public function getSeanceWithEtudiants(int $seanceId, int $enseignantId): Seance
    {
        $seance = $this->seanceRepository->findByIdWithRelations($seanceId, ['classe.etudiants.user', 'matiere', 'typeCours']);

        // Vérifier que la séance appartient à l'enseignant
        if ($seance->enseignant_id !== $enseignantId) {
            throw new \Exception('Cette séance n\'appartient pas à cet enseignant.');
        }

        // Vérifier que la séance est de type "Cours" (présentiel)
        $typeCours = strtolower($seance->typeCours->nom_type_cours);
        if ($typeCours !== 'cours') {
            throw new \Exception('La saisie des présences est autorisée uniquement pour les cours présentiels.');
        }

        // Vérifier la contrainte de 2 semaines pour les cours présentiels
        if ($typeCours === 'cours') {
            $dateSeance = Carbon::parse($seance->date_seance);
            $dateLimit = Carbon::now()->subWeeks(2);

            if ($dateSeance->lt($dateLimit)) {
                throw new \Exception('La saisie des présences n\'est plus possible pour cette séance (délai de 2 semaines dépassé).');
            }
        }

        return $seance;
    }

    /**
     * Récupérer les statuts de présence disponibles
     */
    public function getStatutsPresence(): Collection
    {
        return StatutPresence::whereIn('nom_statut_presence', ['présent', 'retard', 'absent'])->get();
    }

    /**
     * Récupérer les présences pour une séance
     */
    public function getPresencesForSeance(int $seanceId): Collection
    {
        return $this->presenceService->getPresencesForSeance($seanceId);
    }

    /**
     * Enregistrer les présences pour une séance
     */
    public function enregistrerPresences(int $seanceId, int $enseignantId, array $presencesData): Collection
    {
        // Récupérer la séance pour obtenir le coordinateur_id
        $seance = Seance::findOrFail($seanceId);

        // Vérifier que la séance appartient à l'enseignant
        if ($seance->enseignant_id !== $enseignantId) {
            throw new \Exception('Cette séance n\'appartient pas à cet enseignant.');
        }

        return $this->presenceService->storePresences($seanceId, $seance->coordinateur_id, $presencesData);
    }

    /**
     * Récupérer l'emploi du temps d'un enseignant
     */
    public function getEmploiDuTemps(int $enseignantId, ?string $type = 'tous'): array
    {
        $query = Seance::where('enseignant_id', $enseignantId)
            ->with(['classe', 'matiere', 'typeCours', 'statutSeance'])
            ->orderBy('date_seance', 'asc')
            ->orderBy('heure_debut', 'asc');

        // Filtrer selon le type (passé ou à venir)
        $maintenant = Carbon::now();

        if ($type === 'passé') {
            $query->where(function($q) use ($maintenant) {
                $q->whereDate('date_seance', '<', $maintenant->format('Y-m-d'))
                  ->orWhere(function($q2) use ($maintenant) {
                      $q2->whereDate('date_seance', '=', $maintenant->format('Y-m-d'))
                         ->whereTime('heure_fin', '<', $maintenant->format('H:i:s'));
                  });
            });
        } elseif ($type === 'avenir') {
            $query->where(function($q) use ($maintenant) {
                $q->whereDate('date_seance', '>', $maintenant->format('Y-m-d'))
                  ->orWhere(function($q2) use ($maintenant) {
                      $q2->whereDate('date_seance', '=', $maintenant->format('Y-m-d'))
                         ->whereTime('heure_debut', '>=', $maintenant->format('H:i:s'));
                  });
            });
        }

        $seances = $query->get();

        // Organiser les séances par jour et période
        $emploiDuTemps = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

        foreach ($seances as $seance) {
            $dateSeance = Carbon::parse($seance->date_seance);
            $jour = ucfirst($dateSeance->locale('fr')->dayName);
            $periode = Carbon::parse($seance->heure_debut)->hour < 12 ? 'matin' : 'soir';

            if (!isset($emploiDuTemps[$jour])) {
                $emploiDuTemps[$jour] = [];
            }

            if (!isset($emploiDuTemps[$jour][$periode])) {
                $emploiDuTemps[$jour][$periode] = [];
            }

            $emploiDuTemps[$jour][$periode] = [
                'id' => $seance->id,
                'cours' => $seance->matiere->nom_matiere,
                'enseignant' => $seance->enseignant->user->nom,
                'type' => $seance->typeCours->nom_type_cours,
                'heure_debut' => $seance->heure_debut,
                'heure_fin' => $seance->heure_fin,
                'statut_id' => $seance->statut_seance_id,
                'statut' => $seance->statutSeance ? $seance->statutSeance->nom_statut : 'Planifiée',
                'date_seance' => $seance->date_seance,
                'classe' => $seance->classe->nom_classe,
                'est_passee' => $dateSeance->format('Y-m-d') < $maintenant->format('Y-m-d') ||
                               ($dateSeance->format('Y-m-d') == $maintenant->format('Y-m-d') &&
                                Carbon::parse($seance->heure_fin)->format('H:i:s') < $maintenant->format('H:i:s')),
                'est_courante' => $dateSeance->format('Y-m-d') == $maintenant->format('Y-m-d') &&
                                 Carbon::parse($seance->heure_debut)->format('H:i:s') <= $maintenant->format('H:i:s') &&
                                 Carbon::parse($seance->heure_fin)->format('H:i:s') >= $maintenant->format('H:i:s'),
                'est_future' => $dateSeance->format('Y-m-d') > $maintenant->format('Y-m-d') ||
                               ($dateSeance->format('Y-m-d') == $maintenant->format('Y-m-d') &&
                                Carbon::parse($seance->heure_debut)->format('H:i:s') > $maintenant->format('H:i:s'))
            ];
        }

        return $emploiDuTemps;
    }

    /**
     * Récupérer les étudiants droppés (taux de présence < 30%)
     */
    public function getEtudiantsDroppes(int $enseignantId): Collection
    {
        // Récupérer les classes de l'enseignant
        $classes = Seance::where('enseignant_id', $enseignantId)
            ->distinct()
            ->pluck('classe_id');

        // Récupérer les étudiants de ces classes
        $etudiants = Etudiant::with('user')
            ->whereHas('classes', function ($query) use ($classes) {
                $query->whereIn('classes.id', $classes);
            })
            ->whereHas('presences')
            ->get();

        // Calculer le taux de présence pour chaque étudiant
        $etudiantsDroppes = $etudiants->map(function ($etudiant) {
            $total = $etudiant->presences()->count();
            $absents = $etudiant->presences()
                ->where('statut_presence_id', 2) // 2 = Absent
                ->count();

            $taux = $total > 0 ? round(100 - ($absents / $total * 100), 1) : 0;

            // Déterminer la couleur en fonction du taux
            $couleur = '#f44336'; // Rouge par défaut (taux < 30%)
            if ($taux >= 70) {
                $couleur = '#1a5d1a'; // Vert foncé
            } elseif ($taux >= 50.1) {
                $couleur = '#4caf50'; // Vert clair
            } elseif ($taux >= 30.1) {
                $couleur = '#ff9800'; // Orange
            }

            return [
                'id' => $etudiant->id,
                'nom' => $etudiant->user->nom ?? 'Étudiant ' . $etudiant->id,
                'photo' => $etudiant->user->photo_url ?? 'https://randomuser.me/api/portraits/men/'.($etudiant->id % 100).'.jpg',
                'taux' => $taux,
                'couleur' => $couleur,
                'dropped' => $taux < 30,
            ];
        })
        ->filter(fn ($e) => $e['dropped'])
        ->values();

        return collect($etudiantsDroppes);
    }
}
