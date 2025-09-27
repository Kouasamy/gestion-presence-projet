<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SeanceService
{
    /**
     * Créer une nouvelle séance
     */
    public function createSeance(array $data): Seance
    {
        $data['coordinateur_id'] = Auth::user()->coordinateur->id;

        return Seance::create($data);
    }

    /**
     * Mettre à jour une séance existante
     */
    public function updateSeance(Seance $seance, array $data): Seance
    {
        $seance->update($data);

        return $seance;
    }

    /**
     * Supprimer une séance
     */
    public function deleteSeance(Seance $seance): ?bool
    {
        return $seance->delete();
    }

    /**
     * Reporter une séance
     */
    public function reporterSeance(Seance $seance, array $data): Seance
    {
        // Sauvegarder l'historique du report
        $seance->historiqueReports()->create([
            'date_initiale' => $seance->date_seance,
            'heure_debut_initiale' => $seance->heure_debut,
            'heure_fin_initiale' => $seance->heure_fin,
            'coordinateur_id' => Auth::user()->coordinateur->id,
        ]);

        // Mettre à jour la séance
        $seance->update([
            'date_seance' => $data['nouvelle_date'],
            'heure_debut' => $data['heure_debut'],
            'heure_fin' => $data['heure_fin'],
            'statut_seance_id' => 4, // ID pour "Reportée"
        ]);

        return $seance;
    }

    /**
     * Annuler une séance
     */
    public function annulerSeance(Seance $seance): Seance
    {
        $seance->update([
            'statut_seance_id' => 3, // ID pour "Annulée"
        ]);

        return $seance;
    }

    /**
     * Récupérer les séances pour un coordinateur
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSeancesForCoordinateur(array $filters = [])
    {
        $query = Seance::where('coordinateur_id', Auth::user()->coordinateur->id)
            ->with(['classe', 'matiere', 'enseignant.user', 'typeCours', 'statutSeance']);

        // Si on demande la vue emploi du temps
        if (isset($filters['view']) && $filters['view'] === 'timetable') {
            return $query->paginate(10);
        }

        // Filtre par type (attendance ou justification)
        if (isset($filters['filter'])) {
            if ($filters['filter'] === 'attendance') {
                $query->whereHas('presences', function ($q) {
                    $q->whereDoesntHave('justificationAbsence');
                });
            } elseif ($filters['filter'] === 'justification') {
                $query->whereHas('presences', function ($q) {
                    $q->whereHas('justificationAbsence');
                });
            }
        }

        // Filtre par classe
        if (isset($filters['classe']) && ! empty($filters['classe'])) {
            $query->where('classe_id', $filters['classe']);
        }

        // Filtre par date
        if (isset($filters['date']) && ! empty($filters['date'])) {
            $query->whereDate('date_seance', $filters['date']);
        }

        // Filtre par type de cours
        if (isset($filters['type']) && ! empty($filters['type'])) {
            $query->whereHas('typeCours', function ($q) use ($filters) {
                $q->where('nom_type_cours', $filters['type']);
            });
        }

        return $query->orderBy('date_seance', 'desc')
            ->orderBy('heure_debut', 'asc')
            ->paginate(10);
    }

    /**
     * Récupérer les séances pour une classe spécifique
     */
    public function getSeancesForClasse(int $classeId, array $filters = []): Collection
    {
        $query = Seance::where('classe_id', $classeId)
            ->with(['matiere', 'enseignant.user', 'typeCours', 'statutSeance']);

        // Filtre par date
        if (isset($filters['date_debut']) && ! empty($filters['date_debut'])) {
            $query->whereDate('date_seance', '>=', $filters['date_debut']);
        }
        if (isset($filters['date_fin']) && ! empty($filters['date_fin'])) {
            $query->whereDate('date_seance', '<=', $filters['date_fin']);
        }

        // Filtre par type de cours
        if (isset($filters['type_cours_id']) && ! empty($filters['type_cours_id'])) {
            $query->where('type_cours_id', $filters['type_cours_id']);
        }

        // Filtre par enseignant
        if (isset($filters['enseignant_id']) && ! empty($filters['enseignant_id'])) {
            $query->where('enseignant_id', $filters['enseignant_id']);
        }

        // Filtre par matière
        if (isset($filters['matiere_id']) && ! empty($filters['matiere_id'])) {
            $query->where('matiere_id', $filters['matiere_id']);
        }

        return $query->orderBy('date_seance')
            ->orderBy('heure_debut')
            ->get();
    }

    /**
     * Créer un emploi du temps
     *
     * @throws \Exception
     */
    public function createEmploiDuTemps(array $data): array
    {
        $coordinateur = Auth::user()->coordinateur;
        if (! $coordinateur) {
            throw new \Exception('Utilisateur non autorisé');
        }

        $classeId = $data['classe_id'];
        $seancesCreees = [];

        DB::beginTransaction();
        try {
            foreach ($data['seances'] as $key => $seanceData) {
                // Vérifier que les données nécessaires sont présentes
                if (! empty($seanceData['matiere_id']) && ! empty($seanceData['date_seance']) &&
                    ! empty($seanceData['heure_debut']) && ! empty($seanceData['heure_fin'])) {

                    $heureDebut = $seanceData['heure_debut'];
                    $heureFin = $seanceData['heure_fin'];

                    // Vérifier les conflits d'horaire pour la classe (en excluant les séances annulées ou reportées)
                    // Mais permettre plusieurs emplois du temps pour la même classe sur des semaines différentes
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

                    // Si un conflit est détecté, vérifier si c'est une séance existante ou une nouvelle séance
                    if ($conflitClasse) {
                        // Vérifier si la séance en conflit est dans la même semaine
                        $dateSeanceConflit = Carbon::parse($conflitClasse->date_seance);
                        $dateSeanceNouvelle = Carbon::parse($seanceData['date_seance']);

                        // Si les séances sont dans la même semaine, c'est un conflit
                        if ($dateSeanceConflit->weekOfYear == $dateSeanceNouvelle->weekOfYear &&
                            $dateSeanceConflit->year == $dateSeanceNouvelle->year) {
                            throw new \Exception("Conflit d'horaire pour la classe à la date {$seanceData['date_seance']} ({$heureDebut}-{$heureFin}) - Une séance est déjà programmée à cet horaire");
                        }
                        // Sinon, c'est un emploi du temps pour une semaine différente, donc pas de conflit
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

                    // Si un conflit est détecté pour l'enseignant, vérifier si c'est dans la même semaine
                    if ($conflitEnseignant) {
                        // Vérifier si la séance en conflit est dans la même semaine
                        $dateSeanceConflit = Carbon::parse($conflitEnseignant->date_seance);
                        $dateSeanceNouvelle = Carbon::parse($seanceData['date_seance']);

                        // Si les séances sont dans la même semaine, c'est un conflit
                        if ($dateSeanceConflit->weekOfYear == $dateSeanceNouvelle->weekOfYear &&
                            $dateSeanceConflit->year == $dateSeanceNouvelle->year) {
                            throw new \Exception("Conflit d'horaire pour l'enseignant à la date {$seanceData['date_seance']} ({$heureDebut}-{$heureFin})");
                        }
                        // Sinon, c'est un emploi du temps pour une semaine différente, donc pas de conflit
                    }

                    // Créer la séance
                    $seance = Seance::create([
                        'classe_id' => $classeId,
                        'matiere_id' => $seanceData['matiere_id'],
                        'enseignant_id' => $seanceData['enseignant_id'],
                        'type_cours_id' => $seanceData['type_cours_id'],
                        'coordinateur_id' => $coordinateur->id,
                        'date_seance' => $seanceData['date_seance'],
                        'heure_debut' => $heureDebut,
                        'heure_fin' => $heureFin,
                        'statut_seance_id' => 1, // Statut par défaut
                    ]);

                    $seancesCreees[] = $seance;

                    // Si nb_semaines est défini, créer des séances répétitives
                    // Mais vérifier d'abord s'il existe déjà des séances pour ces dates
                    if (isset($data['nb_semaines']) && $data['nb_semaines'] > 1) {
                        $nbSemaines = (int) $data['nb_semaines'];
                        $dateSeance = Carbon::parse($seanceData['date_seance']);

                        for ($i = 1; $i < $nbSemaines; $i++) {
                            $nouvelleDate = $dateSeance->copy()->addWeeks($i);

                            // Vérifier s'il existe déjà une séance pour cette date, cette classe, cette matière et cet horaire
                            $seanceExistante = Seance::where('classe_id', $classeId)
                                ->where('date_seance', $nouvelleDate->format('Y-m-d'))
                                ->where('heure_debut', $heureDebut)
                                ->where('heure_fin', $heureFin)
                                ->first();

                            // Ne créer la séance que si elle n'existe pas déjà
                            if (!$seanceExistante) {
                                $seanceRepetee = Seance::create([
                                    'classe_id' => $classeId,
                                    'matiere_id' => $seanceData['matiere_id'],
                                    'enseignant_id' => $seanceData['enseignant_id'],
                                    'type_cours_id' => $seanceData['type_cours_id'],
                                    'coordinateur_id' => $coordinateur->id,
                                    'date_seance' => $nouvelleDate->format('Y-m-d'),
                                    'heure_debut' => $heureDebut,
                                    'heure_fin' => $heureFin,
                                    'statut_seance_id' => 1, // Statut par défaut
                                ]);

                                $seancesCreees[] = $seanceRepetee;
                            }
                        }
                    }
                }
            }

            DB::commit();

            return $seancesCreees;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Mettre à jour un emploi du temps
     *
     * @throws \Exception
     */
    public function updateEmploiDuTemps(int $classeId, array $data): array
    {
        $seancesMisesAJour = [];

        DB::beginTransaction();
        try {
            foreach ($data['seances'] as $seanceData) {
                // Vérifier si c'est une séance existante (avec ID) ou une nouvelle séance
                if (isset($seanceData['id'])) {
                    // Mise à jour d'une séance existante
                    $seance = Seance::findOrFail($seanceData['id']);

                    // Vérifier les conflits d'horaire pour la classe et l'enseignant
                    $heureDebut = $seanceData['heure_debut'];
                    $heureFin = $seanceData['heure_fin'];

                    // Vérifier les conflits pour la classe (en excluant la séance en cours de modification)
                    $conflitClasse = Seance::where('classe_id', $classeId)
                        ->where('date_seance', $seanceData['date_seance'])
                        ->where('id', '!=', $seance->id)
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
                        // Vérifier si la séance en conflit est dans la même semaine
                        $dateSeanceConflit = Carbon::parse($conflitClasse->date_seance);
                        $dateSeanceNouvelle = Carbon::parse($seanceData['date_seance']);

                        // Si les séances sont dans la même semaine, c'est un conflit
                        if ($dateSeanceConflit->weekOfYear == $dateSeanceNouvelle->weekOfYear &&
                            $dateSeanceConflit->year == $dateSeanceNouvelle->year) {
                            throw new \Exception("Conflit d'horaire pour la classe à la date {$seanceData['date_seance']} ({$heureDebut}-{$heureFin}) - Une séance est déjà programmée à cet horaire");
                        }
                    }

                    // Vérifier les conflits pour l'enseignant (en excluant la séance en cours de modification)
                    $conflitEnseignant = Seance::where('enseignant_id', $seanceData['enseignant_id'])
                        ->where('date_seance', $seanceData['date_seance'])
                        ->where('id', '!=', $seance->id)
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
                        // Vérifier si la séance en conflit est dans la même semaine
                        $dateSeanceConflit = Carbon::parse($conflitEnseignant->date_seance);
                        $dateSeanceNouvelle = Carbon::parse($seanceData['date_seance']);

                        // Si les séances sont dans la même semaine, c'est un conflit
                        if ($dateSeanceConflit->weekOfYear == $dateSeanceNouvelle->weekOfYear &&
                            $dateSeanceConflit->year == $dateSeanceNouvelle->year) {
                            throw new \Exception("Conflit d'horaire pour l'enseignant à la date {$seanceData['date_seance']} ({$heureDebut}-{$heureFin})");
                        }
                    }

                    $seance->update([
                        'classe_id' => $classeId,
                        'matiere_id' => $seanceData['matiere_id'],
                        'enseignant_id' => $seanceData['enseignant_id'],
                        'type_cours_id' => $seanceData['type_cours_id'],
                        'date_seance' => $seanceData['date_seance'],
                        'heure_debut' => $heureDebut,
                        'heure_fin' => $heureFin,
                    ]);

                    $seancesMisesAJour[] = $seance;
                } else {
                    // Pour les nouvelles séances, utiliser la même logique que dans createEmploiDuTemps
                    $heureDebut = $seanceData['heure_debut'];
                    $heureFin = $seanceData['heure_fin'];

                    // Vérifier les conflits d'horaire pour la classe
                    $conflitClasse = Seance::where('classe_id', $classeId)
                        ->where('date_seance', $seanceData['date_seance'])
                        ->whereNotIn('statut_seance_id', [3, 4])
                        ->where(function ($query) use ($heureDebut, $heureFin) {
                            $query->whereBetween('heure_debut', [$heureDebut, $heureFin])
                                ->orWhereBetween('heure_fin', [$heureDebut, $heureFin])
                                ->orWhere(function ($q) use ($heureDebut, $heureFin) {
                                    $q->where('heure_debut', '<=', $heureDebut)
                                        ->where('heure_fin', '>=', $heureFin);
                                });
                        })->first();

                    if ($conflitClasse) {
                        // Vérifier si la séance en conflit est dans la même semaine
                        $dateSeanceConflit = Carbon::parse($conflitClasse->date_seance);
                        $dateSeanceNouvelle = Carbon::parse($seanceData['date_seance']);

                        if ($dateSeanceConflit->weekOfYear == $dateSeanceNouvelle->weekOfYear &&
                            $dateSeanceConflit->year == $dateSeanceNouvelle->year) {
                            throw new \Exception("Conflit d'horaire pour la classe à la date {$seanceData['date_seance']} ({$heureDebut}-{$heureFin}) - Une séance est déjà programmée à cet horaire");
                        }
                    }

                    // Vérifier les conflits d'horaire pour l'enseignant
                    $conflitEnseignant = Seance::where('enseignant_id', $seanceData['enseignant_id'])
                        ->where('date_seance', $seanceData['date_seance'])
                        ->whereNotIn('statut_seance_id', [3, 4])
                        ->where(function ($query) use ($heureDebut, $heureFin) {
                            $query->whereBetween('heure_debut', [$heureDebut, $heureFin])
                                ->orWhereBetween('heure_fin', [$heureDebut, $heureFin])
                                ->orWhere(function ($q) use ($heureDebut, $heureFin) {
                                    $q->where('heure_debut', '<=', $heureDebut)
                                        ->where('heure_fin', '>=', $heureFin);
                                });
                        })->first();

                    if ($conflitEnseignant) {
                        // Vérifier si la séance en conflit est dans la même semaine
                        $dateSeanceConflit = Carbon::parse($conflitEnseignant->date_seance);
                        $dateSeanceNouvelle = Carbon::parse($seanceData['date_seance']);

                        if ($dateSeanceConflit->weekOfYear == $dateSeanceNouvelle->weekOfYear &&
                            $dateSeanceConflit->year == $dateSeanceNouvelle->year) {
                            throw new \Exception("Conflit d'horaire pour l'enseignant à la date {$seanceData['date_seance']} ({$heureDebut}-{$heureFin})");
                        }
                    }

                    // Création d'une nouvelle séance
                    $seance = Seance::create([
                        'classe_id' => $classeId,
                        'matiere_id' => $seanceData['matiere_id'],
                        'enseignant_id' => $seanceData['enseignant_id'],
                        'type_cours_id' => $seanceData['type_cours_id'],
                        'coordinateur_id' => Auth::user()->coordinateur->id,
                        'date_seance' => $seanceData['date_seance'],
                        'heure_debut' => $heureDebut,
                        'heure_fin' => $heureFin,
                        'statut_seance_id' => 1, // Statut par défaut
                    ]);

                    $seancesMisesAJour[] = $seance;
                }
            }

            DB::commit();
            return $seancesMisesAJour;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Supprimer un emploi du temps pour une semaine spécifique
     */
    public function deleteEmploiDuTemps(int $classeId, ?string $dateDebut = null): bool
    {
        // Si aucune date n'est spécifiée, utiliser la date actuelle
        if (!$dateDebut) {
            $dateDebut = Carbon::now()->startOfWeek()->format('Y-m-d');
        }

        $dateDebutObj = Carbon::parse($dateDebut);
        $dateFinObj = $dateDebutObj->copy()->endOfWeek();

        // Récupérer uniquement les séances de la semaine spécifiée
        $seances = Seance::where('classe_id', $classeId)
            ->whereDate('date_seance', '>=', $dateDebutObj->format('Y-m-d'))
            ->whereDate('date_seance', '<=', $dateFinObj->format('Y-m-d'))
            ->get();

        foreach ($seances as $seance) {
            $seance->delete();
        }

        return true;
    }

    /**
     * Récupérer les emplois du temps passés et à venir pour un utilisateur
     *
     * @param string $role Le rôle de l'utilisateur (coordinateur, enseignant, etudiant)
     * @param int $userId L'ID de l'utilisateur
     * @param string $type Le type d'emploi du temps à récupérer (passé, avenir, tous)
     * @return array
     */
    public function getEmploisDuTemps(string $role, int $userId, string $type = 'tous'): array
    {
        $query = Seance::query()->with(['classe', 'matiere', 'enseignant.user', 'typeCours', 'statutSeance']);

        // Filtrer selon le rôle
        if ($role === 'enseignant') {
            $query->where('enseignant_id', $userId);
        } elseif ($role === 'coordinateur') {
            $query->where('coordinateur_id', $userId);
        } elseif ($role === 'etudiant') {
            // Pour un étudiant, on récupère sa classe
            $etudiant = Etudiant::findOrFail($userId);
            $query->where('classe_id', $etudiant->classe_id);
        }

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

        // Ordonner les résultats
        $query->orderBy('date_seance')->orderBy('heure_debut');

        // Regrouper par semaine
        $seances = $query->get();
        $emploisDuTemps = [];

        foreach ($seances as $seance) {
            $dateSeance = Carbon::parse($seance->date_seance);
            $numeroSemaine = $dateSeance->weekOfYear;
            $debutSemaine = $dateSeance->copy()->startOfWeek()->format('Y-m-d');
            $finSemaine = $dateSeance->copy()->endOfWeek()->format('Y-m-d');

            // Clé pour la semaine
            $cleSeance = "semaine_{$numeroSemaine}_{$debutSemaine}_{$finSemaine}";

            if (!isset($emploisDuTemps[$cleSeance])) {
                $emploisDuTemps[$cleSeance] = [
                    'debut_semaine' => $debutSemaine,
                    'fin_semaine' => $finSemaine,
                    'numero_semaine' => $numeroSemaine,
                    'est_passee' => $dateSeance->endOfWeek() < $maintenant,
                    'est_courante' => $dateSeance->startOfWeek() <= $maintenant && $dateSeance->endOfWeek() >= $maintenant,
                    'est_future' => $dateSeance->startOfWeek() > $maintenant,
                    'seances' => []
                ];
            }

            $emploisDuTemps[$cleSeance]['seances'][] = [
                'id' => $seance->id,
                'date' => $seance->date_seance,
                'jour' => ucfirst($dateSeance->locale('fr')->dayName),
                'periode' => Carbon::parse($seance->heure_debut)->hour < 12 ? 'matin' : 'soir',
                'heure_debut' => $seance->heure_debut,
                'heure_fin' => $seance->heure_fin,
                'matiere' => $seance->matiere->nom_matiere,
                'enseignant' => $seance->enseignant->user->nom,
                'type_cours' => $seance->typeCours->nom_type_cours,
                'classe' => $seance->classe->nom_classe,
                'statut' => $seance->statutSeance ? $seance->statutSeance->nom_statut : 'Planifiée',
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

        return $emploisDuTemps;
    }

    /**
     * Récupérer l'emploi du temps pour une classe
     */
    public function getEmploiDuTempsForClasse(int $classeId, ?string $dateDebut = null): array
    {
        // Si aucune date de début n'est spécifiée, utiliser la date actuelle
        if (!$dateDebut) {
            $dateDebut = Carbon::now()->format('Y-m-d');
        }

        $dateDebutObj = Carbon::parse($dateDebut);
        $dateFinObj = $dateDebutObj->copy()->addDays(6);

        $query = Seance::where('classe_id', $classeId)
            ->with(['matiere', 'enseignant.user', 'typeCours', 'statutSeance'])
            ->whereDate('date_seance', '>=', $dateDebutObj->format('Y-m-d'))
            ->whereDate('date_seance', '<=', $dateFinObj->format('Y-m-d'))
            ->orderBy('date_seance')
            ->orderBy('heure_debut');

        $seances = $query->get();
        $emploiDuTemps = [];
        $maintenant = Carbon::now();

        foreach ($seances as $seance) {
            $dateSeance = Carbon::parse($seance->date_seance);
            $jour = ucfirst($dateSeance->locale('fr')->dayName);
            $periode = Carbon::parse($seance->heure_debut)->hour < 12 ? 'matin' : 'soir';

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
}
