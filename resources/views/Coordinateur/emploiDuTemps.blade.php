@extends('layouts.coordinateur')

@section('title', 'Emploi du temps')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 relative">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <a href="{{ route('coordinateur.emploiDuTemps.create') }}"
                   class="absolute top-6 right-6 text-violet-700 hover:text-violet-900 font-semibold underline transition-colors text-lg">
                    Créer un emploi du temps
                </a>
                <h1 class="text-xl font-semibold text-gray-800 mb-6">
                    Emploi du temps - Visualisation
                </h1>

                <!-- Sélection de classe -->
                <div class="mb-6">
                    <label for="classe_select" class="block text-gray-700 font-semibold mb-2">Sélectionner la classe</label>
                    <select id="classe_select" class="w-full border-gray-300 rounded-md shadow-sm text-lg" required>
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}">{{ $classe->nom_classe }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Emploi du temps -->
                <div id="timetable_container" class="hidden">
                    <table class="w-full border-collapse bg-[#2f3357] text-white rounded-lg overflow-hidden mb-6 text-lg" style="min-width:1200px;">
                        <thead>
                            <tr>
                                <th class="p-6 bg-[#262944] font-semibold text-xl">Horaires</th>
                                <th class="p-6 bg-[#262944] font-semibold text-xl">Lundi</th>
                                <th class="p-6 bg-[#262944] font-semibold text-xl">Mardi</th>
                                <th class="p-6 bg-[#262944] font-semibold text-xl">Mercredi</th>
                                <th class="p-6 bg-[#262944] font-semibold text-xl">Jeudi</th>
                                <th class="p-6 bg-[#262944] font-semibold text-xl">Vendredi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-6 bg-[#262944] text-lg h-20 align-middle">
                                    <div class="font-semibold">Matin</div>
                                    <div class="text-base opacity-80">09h-12h00</div>
                                </td>
                                @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'] as $jour)
                                <td class="p-6 border border-[#3d4270] text-lg h-20 align-middle" id="cell_{{ $jour }}_matin">
                                    <div class="text-center text-gray-400">Aucune séance</div>
                                </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="p-6 bg-[#262944] text-lg h-20 align-middle">
                                    <div class="font-semibold">Après-Midi</div>
                                    <div class="text-base opacity-80">13h30-16h30</div>
                                </td>
                                @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'] as $jour)
                                <td class="p-6 border border-[#3d4270] text-lg h-20 align-middle" id="cell_{{ $jour }}_soir">
                                    <div class="text-center text-gray-400">Aucune séance</div>
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Message quand aucune classe n'est sélectionnée -->
                <div id="no_selection" class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500">Veuillez sélectionner une classe pour afficher son emploi du temps</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Report -->
<div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Reporter la séance</h2>
    <form id="reportForm" action="" method="POST" class="space-y-4">
        @csrf
        @method('POST')
        <input type="hidden" name="seance_id" id="seance_id_report">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle date</label>
                <input type="date" name="nouvelle_date" class="w-full border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle heure de début</label>
                <input type="time" name="heure_debut" class="w-full border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle heure de fin</label>
                <input type="time" name="heure_fin" class="w-full border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Motif du report</label>
                <textarea name="motif_report" class="w-full border-gray-300 rounded-md shadow-sm" rows="3" required></textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeReportModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-[#e11d48] text-white rounded-lg hover:bg-[#be123c]">
                    Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Annulation -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Annuler la séance</h2>
    <form id="cancelForm" action="" method="POST" class="space-y-4">
        @csrf
        @method('POST')
        <input type="hidden" name="seance_id" id="seance_id_cancel">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Motif de l'annulation</label>
                <textarea name="motif_annulation" class="w-full border-gray-300 rounded-md shadow-sm" rows="3" required></textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-[#e11d48] text-white rounded-lg hover:bg-[#be123c]">
                    Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classeSelect = document.getElementById('classe_select');
    const timetableContainer = document.getElementById('timetable_container');
    const noSelection = document.getElementById('no_selection');
    const reportModal = document.getElementById('reportModal');
    const cancelModal = document.getElementById('cancelModal');

    // Set initial classe value if provided in URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialClasseId = '{{ request()->route("classe") }}';
    const dateDebut = urlParams.get('date_debut');

    if (initialClasseId) {
        classeSelect.value = initialClasseId;
        loadTimetable(initialClasseId, dateDebut);
    }

    classeSelect.addEventListener('change', function() {
        const classeId = this.value;
        if (classeId) {
            loadTimetable(classeId, null);
        } else {
            timetableContainer.classList.add('hidden');
            noSelection.classList.remove('hidden');
        }
    });

    function loadTimetable(classeId, dateDebut) {
        let url = `{{ route('coordinateur.emploiDuTemps.show', '') }}/${classeId}`;
        if (dateDebut) {
            url += `?date_debut=${dateDebut}`;
        }
        fetch(url)
            .then(response => response.json())
            .then(data => {
                timetableContainer.classList.remove('hidden');
                noSelection.classList.add('hidden');
                displaySeances(data.seances);
            })
            .catch(error => {
                window.location.reload();
            });
    }

    function displaySeances(seances) {
        // Réinitialiser toutes les cellules
        document.querySelectorAll('[id^="cell_"]').forEach(cell => {
            cell.innerHTML = '<div class="text-center text-gray-400">Aucune séance</div>';
        });

        // Afficher les séances
        seances.forEach(seance => {
            const periode = seance.heure_debut.startsWith('09') ? 'matin' : 'soir';
            const jour = new Date(seance.date_seance).toLocaleString('fr-FR', {weekday: 'long'});
            const cell = document.getElementById(`cell_${jour}_${periode}`);

            if (cell) {
                cell.innerHTML = `
                    <div class="bg-[#3d4270] rounded-lg p-4">
                        <div class="font-semibold">${seance.matiere.nom_matiere}</div>
                        <div class="text-sm opacity-80">${seance.enseignant.user.nom}</div>
                        <div class="text-sm">${seance.type_cours.nom_type_cours}</div>
                        <div class="mt-2 flex justify-end gap-2">
                            <button onclick="openReportModal(${seance.id})"
                                    class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Reporter
                            </button>
                            <button onclick="openCancelModal(${seance.id})"
                                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                Annuler
                            </button>
                        </div>
                    </div>
                `;
            }
        });
    }

    // Gestion du modal de report
    window.openReportModal = function(seanceId) {
        document.getElementById('seance_id_report').value = seanceId;
        reportModal.classList.remove('hidden');
        reportModal.classList.add('flex');
    }

    window.closeReportModal = function() {
        reportModal.classList.add('hidden');
        reportModal.classList.remove('flex');
        document.getElementById('reportForm').reset();
    }

    // Gestion du modal d'annulation
    window.openCancelModal = function(seanceId) {
        document.getElementById('seance_id_cancel').value = seanceId;
        cancelModal.classList.remove('hidden');
        cancelModal.classList.add('flex');
    }

    window.closeCancelModal = function() {
        cancelModal.classList.add('hidden');
        cancelModal.classList.remove('flex');
        document.getElementById('cancelForm').reset();
    }

    // Soumission du formulaire de report
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const seanceId = document.getElementById('seance_id_report').value;
        const formData = new FormData(this);

        const form = document.getElementById('reportForm');
        form.action = `{{ route('coordinateur.seances.reporter', '') }}/${seanceId}`;
        form.submit();
    });

    // Soumission du formulaire d'annulation
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const seanceId = document.getElementById('seance_id_cancel').value;
        const formData = new FormData(this);

        const form = document.getElementById('cancelForm');
        form.action = `{{ route('coordinateur.seances.annuler', '') }}/${seanceId}`;
        form.submit();
    });

    // Fermer les modals si on clique en dehors
    window.onclick = function(event) {
        if (event.target === reportModal) {
            closeReportModal();
        }
        if (event.target === cancelModal) {
            closeCancelModal();
        }
    }
});
</script>
@endpush

<style>
@media print {
    .nav-menu,
    .header-icons,
    #classe_select,
    button {
        display: none !important;
    }

    .max-w-7xl {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .shadow-sm {
        box-shadow: none !important;
    }

    .bg-[#2f3357] {
        background-color: white !important;
        color: black !important;
    }

    .bg-[#262944] {
        background-color: white !important;
        color: black !important;
        border: 1px solid #ddd !important;
    }

    .border-[#3d4270] {
        border-color: #ddd !important;
    }

    .text-white {
        color: black !important;
    }
}
</style>
@endsection
