@extends('layouts.coordinateur')

@section('title', 'Modifier un emploi du temps')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 relative">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-semibold text-gray-800">
                        Modification de l'emploi du temps - {{ $classe->nom_classe }}
                    </h1>
                    <a href="{{ route('coordinateur.emploiDuTemps.index') }}" class="px-4 py-2 bg-[#2f3357] text-white rounded-lg hover:bg-[#262944]">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('coordinateur.emploiDuTemps.update', $classe->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Sélection de la semaine du mois -->
                    <div class="mb-6">
                        <label for="semaine" class="block text-sm font-medium text-gray-700 mb-2">Semaine du mois</label>
                        <select name="semaine" id="semaine" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50" required>
                            @php
                                // Obtenir le mois et l'année en cours
                                $moisActuel = date('n');
                                $anneeActuelle = date('Y');
                                $nomMois = [
                                    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                                ];

                                // Obtenir le premier jour du mois
                                $premierJourDuMois = new DateTime("$anneeActuelle-$moisActuel-01");

                                // Obtenir le nombre de jours dans le mois
                                $nombreDeJours = (int) date('t', strtotime("$anneeActuelle-$moisActuel-01"));

                                // Générer les semaines du mois
                                $semaines = [];
                                $jourCourant = clone $premierJourDuMois;

                                // Trouver le premier lundi (ou le premier jour du mois si c'est un lundi)
                                if ($jourCourant->format('N') != 1) {
                                    // Si le premier jour n'est pas un lundi, reculer jusqu'au lundi précédent
                                    $jourCourant->modify('last monday');
                                }

                                // Générer les semaines
                                $i = 1;
                                while ($i <= 5) { // Maximum 5 semaines dans un mois
                                    $debutSemaine = clone $jourCourant;
                                    $finSemaine = clone $jourCourant;
                                    $finSemaine->modify('+4 days'); // Vendredi

                                    // Vérifier si la semaine contient au moins un jour du mois en cours
                                    $contientJourDuMois = false;
                                    for ($j = 0; $j <= 4; $j++) { // Lundi à vendredi
                                        $jourTest = clone $debutSemaine;
                                        $jourTest->modify("+$j days");
                                        if ($jourTest->format('n') == $moisActuel) {
                                            $contientJourDuMois = true;
                                            break;
                                        }
                                    }

                                    if ($contientJourDuMois) {
                                        $semaines[] = [
                                            'debut' => $debutSemaine->format('Y-m-d'),
                                            'fin' => $finSemaine->format('Y-m-d'),
                                            'label' => 'Semaine ' . $i . ' (' . $debutSemaine->format('d/m') . ' - ' . $finSemaine->format('d/m') . ')'
                                        ];
                                    }

                                    // Passer à la semaine suivante
                                    $jourCourant->modify('+7 days');
                                    $i++;

                                    // Arrêter si on a dépassé le mois
                                    if ($jourCourant->format('n') > $moisActuel && $jourCourant->format('j') > 7) {
                                        break;
                                    }
                                }
                            @endphp

                            <option value="">Sélectionner une semaine</option>
                            @foreach($semaines as $index => $semaine)
                                <option value="{{ $semaine['debut'] }}" {{ $index === 0 ? 'selected' : '' }}>
                                    {{ $semaine['label'] }} - {{ $nomMois[$moisActuel] }} {{ $anneeActuelle }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="date_debut" id="date_debut" value="{{ $semaines[0]['debut'] ?? date('Y-m-d') }}">
                    </div>

                    <!-- Emploi du temps -->
                    <div class="mb-6">
                        <h2 class="text-lg font-medium text-gray-800 mb-4">Modification des séances</h2>

                        <table class="w-full border-collapse bg-[#2f3357] text-white rounded-lg overflow-hidden mb-6 text-lg" style="min-width:1200px;">
                            <thead>
                                <tr>
                                    <th class="p-6 bg-[#262944] font-semibold text-xl">Horaires</th>
                                    @php
                                        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
                                    @endphp
                                    @foreach($jours as $jour)
                                        <th class="p-6 bg-[#262944] font-semibold text-xl">{{ $jour }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['matin' => '09h-12h00', 'soir' => '13h30-16h30'] as $periode => $horaire)
                                    <tr>
                                        <td class="p-6 bg-[#262944] text-lg h-20 align-middle">
                                            <div class="font-semibold">{{ ucfirst($periode) }}</div>
                                            <div class="text-base opacity-80">{{ $horaire }}</div>
                                            <div class="grid grid-cols-2 gap-2 mt-2">
                                                <div>
                                                    <label class="block text-xs font-medium mb-1">Début</label>
                                                    <input type="time" name="horaires[{{ $periode }}][heure_debut]"
                                                        value="{{ $periode == 'matin' ? '09:00' : '13:30' }}"
                                                        class="w-full bg-[#3d4270] border-0 rounded-md shadow-sm focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 text-white text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium mb-1">Fin</label>
                                                    <input type="time" name="horaires[{{ $periode }}][heure_fin]"
                                                        value="{{ $periode == 'matin' ? '12:00' : '16:30' }}"
                                                        class="w-full bg-[#3d4270] border-0 rounded-md shadow-sm focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 text-white text-sm">
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($jours as $jour)
                                            <td class="p-6 border border-[#3d4270] text-lg h-20 align-middle">
                                                @php
                                                    // Rechercher une séance existante pour ce jour et cette période
                                                    $seanceExistante = $seances->first(function($seance) use ($jour, $periode) {
                                                        $jourSeance = ucfirst(\Carbon\Carbon::parse($seance->date_seance)->locale('fr')->dayName);
                                                        $periodeSeance = \Carbon\Carbon::parse($seance->heure_debut)->hour < 12 ? 'matin' : 'soir';
                                                        return $jourSeance === $jour && $periodeSeance === $periode;
                                                    });

                                                    // Générer un index unique pour cette séance
                                                    $index = $seanceExistante ? $seanceExistante->id : 'new_' . $periode . '_' . $jour;
                                                @endphp

                                                <div class="bg-[#3d4270] rounded-lg p-4">
                                                    @if($seanceExistante)
                                                        <input type="hidden" name="seances[{{ $index }}][id]" value="{{ $seanceExistante->id }}">
                                                    @endif
                                                    <input type="hidden" name="seances[{{ $index }}][classe_id]" value="{{ $classe->id }}">
                                                    <input type="hidden" name="seances[{{ $index }}][jour]" value="{{ $jour }}">
                                                    <input type="hidden" name="seances[{{ $index }}][periode]" value="{{ $periode }}">
                                                    <input type="hidden" name="seances[{{ $index }}][date_seance]" class="date-seance" value="{{ $seanceExistante ? $seanceExistante->date_seance : '' }}">

                                                    <div class="mb-3">
                                                        <label class="block text-sm font-medium mb-1">Matière</label>
                                                        <select name="seances[{{ $index }}][matiere_id]" class="w-full bg-[#3d4270] border-0 rounded-md shadow-sm focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 text-white">
                                                            <option value="">Sélectionner</option>
                                                            @foreach($matieres as $matiere)
                                                                <option value="{{ $matiere->id }}" {{ $seanceExistante && $seanceExistante->matiere_id == $matiere->id ? 'selected' : '' }}>
                                                                    {{ $matiere->nom_matiere }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="block text-sm font-medium mb-1">Enseignant</label>
                                                        <select name="seances[{{ $index }}][enseignant_id]" class="w-full bg-[#3d4270] border-0 rounded-md shadow-sm focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 text-white">
                                                            <option value="">Sélectionner</option>
                                                            @foreach($enseignants as $enseignant)
                                                                <option value="{{ $enseignant->id }}" {{ $seanceExistante && $seanceExistante->enseignant_id == $enseignant->id ? 'selected' : '' }}>
                                                                    {{ $enseignant->user->nom }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="block text-sm font-medium mb-1">Type</label>
                                                        <select name="seances[{{ $index }}][type_cours_id]" class="w-full bg-[#3d4270] border-0 rounded-md shadow-sm focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 text-white">
                                                            <option value="">Sélectionner</option>
                                                            @foreach($typesCours as $type)
                                                                <option value="{{ $type->id }}" {{ $seanceExistante && $seanceExistante->type_cours_id == $type->id ? 'selected' : '' }}>
                                                                    {{ $type->nom_type_cours }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <!-- Les heures sont définies au niveau de la période -->
                                                    <input type="hidden" name="seances[{{ $index }}][heure_debut]"
                                                        value="{{ $seanceExistante ? \Carbon\Carbon::parse($seanceExistante->heure_debut)->format('H:i') : ($periode == 'matin' ? '09:00' : '13:30') }}"
                                                        class="periode-{{ $periode }}-debut">
                                                    <input type="hidden" name="seances[{{ $index }}][heure_fin]"
                                                        value="{{ $seanceExistante ? \Carbon\Carbon::parse($seanceExistante->heure_fin)->format('H:i') : ($periode == 'matin' ? '12:00' : '16:30') }}"
                                                        class="periode-{{ $periode }}-fin">

                                                    @if($seanceExistante)
                                                        <div class="mt-3 flex justify-end">
                                                            <a href="{{ route('coordinateur.seances.edit', $seanceExistante->id) }}" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 mr-2">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('coordinateur.seances.destroy', $seanceExistante->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette séance ?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="px-2 py-1 bg-[#e11d48] text-white rounded hover:bg-[#be123c]">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-[#e11d48] text-white rounded-md hover:bg-[#be123c]">
                            <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Récupérer les champs d'heure pour chaque période
        const horaireMatinDebut = document.querySelector('input[name="horaires[matin][heure_debut]"]');
        const horaireMatinFin = document.querySelector('input[name="horaires[matin][heure_fin]"]');
        const horaireSoirDebut = document.querySelector('input[name="horaires[soir][heure_debut]"]');
        const horaireSoirFin = document.querySelector('input[name="horaires[soir][heure_fin]"]');

        // Récupérer tous les champs cachés pour les heures de début et de fin
        const champsMatinDebut = document.querySelectorAll('.periode-matin-debut');
        const champsMatinFin = document.querySelectorAll('.periode-matin-fin');
        const champsSoirDebut = document.querySelectorAll('.periode-soir-debut');
        const champsSoirFin = document.querySelectorAll('.periode-soir-fin');

        // Récupérer le sélecteur de semaine, le champ de date caché et tous les champs de date de séance
        const semaineSelect = document.getElementById('semaine');
        const dateDebutInput = document.getElementById('date_debut');
        const champsDateSeance = document.querySelectorAll('.date-seance');

        // Fonction pour mettre à jour les champs cachés
        function mettreAJourChamps(champs, valeur) {
            champs.forEach(champ => {
                champ.value = valeur;
            });
        }

        // Fonction pour calculer les dates des séances en fonction de la semaine sélectionnée
        function calculerDatesSeances() {
            // Récupérer la date de début de la semaine sélectionnée
            const dateDebutSemaine = semaineSelect.value || dateDebutInput.value;
            if (!dateDebutSemaine) return;

            // Mettre à jour le champ caché date_debut
            dateDebutInput.value = dateDebutSemaine;

            const lundi = new Date(dateDebutSemaine);

            // Ordre des jours dans le tableau
            const jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

            // Mettre à jour les dates des séances
            champsDateSeance.forEach(champ => {
                // Ne pas mettre à jour les champs qui ont déjà une valeur (séances existantes)
                if (champ.value) return;

                // Extraire le jour de la séance à partir du nom du champ
                const nomChamp = champ.name;
                let jourSeance = '';

                for (const jour of jours) {
                    if (nomChamp.includes(jour)) {
                        jourSeance = jour;
                        break;
                    }
                }

                if (jourSeance) {
                    const indexJour = jours.indexOf(jourSeance);
                    const dateSeance = new Date(lundi);
                    dateSeance.setDate(lundi.getDate() + indexJour);

                    // Format YYYY-MM-DD
                    const dateFormatee = dateSeance.toISOString().split('T')[0];
                    champ.value = dateFormatee;
                }
            });
        }

        // Ajouter des écouteurs d'événements pour les changements d'heure
        if (horaireMatinDebut) {
            horaireMatinDebut.addEventListener('change', function() {
                mettreAJourChamps(champsMatinDebut, this.value);
            });
        }

        if (horaireMatinFin) {
            horaireMatinFin.addEventListener('change', function() {
                mettreAJourChamps(champsMatinFin, this.value);
            });
        }

        if (horaireSoirDebut) {
            horaireSoirDebut.addEventListener('change', function() {
                mettreAJourChamps(champsSoirDebut, this.value);
            });
        }

        if (horaireSoirFin) {
            horaireSoirFin.addEventListener('change', function() {
                mettreAJourChamps(champsSoirFin, this.value);
            });
        }

        // Calculer les dates des séances au chargement et à chaque changement de semaine
        calculerDatesSeances();
        semaineSelect.addEventListener('change', calculerDatesSeances);
    });
</script>
@endpush

@endsection
