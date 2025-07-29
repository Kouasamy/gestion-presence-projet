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
                    <select id="classe_select" class="w-full border-gray-300 rounded-md shadow-sm text-lg" onchange="location = this.value;">
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $c)
                            <option value="{{ route('coordinateur.emploiDuTemps.show', ['classe' => $c->id]) }}" @if(isset($classe) && $classe->id == $c->id) selected @endif>{{ $c->nom_classe }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Emploi du temps -->
                <table class="w-full border-collapse bg-[#2f3357] text-white rounded-lg overflow-hidden mb-6 text-lg" style="min-width:1200px;">
                    <thead>
                        <tr>
                            <th class="p-6 bg-[#262944] font-semibold text-xl">Horaires</th>
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
                                </td>
                                @foreach($jours as $jour)
                                    <td class="p-6 border border-[#3d4270] text-lg h-20 align-middle">
                                        @if(isset($emploiDuTemps[$jour][$periode]))
                                            <div class="bg-[#3d4270] rounded-lg p-4">
                                                <div class="font-semibold">{{ $emploiDuTemps[$jour][$periode]['cours'] }}</div>
                                                <div class="text-sm opacity-80">{{ $emploiDuTemps[$jour][$periode]['enseignant'] }}</div>
                                                <div class="text-sm">{{ $emploiDuTemps[$jour][$periode]['type'] }}</div>
                                                <div class="text-sm mt-2">
                                                    {{ \Carbon\Carbon::parse($emploiDuTemps[$jour][$periode]['heure_debut'])->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($emploiDuTemps[$jour][$periode]['heure_fin'])->format('H:i') }}
                                                </div>
                                                @if($emploiDuTemps[$jour][$periode]['statut_id'] == 3)
                                                    <div class="mt-2 text-red-400 font-medium">
                                                        Annulée
                                                    </div>
                                                @elseif($emploiDuTemps[$jour][$periode]['statut_id'] == 4)
                                                    <div class="mt-2 text-yellow-400 font-medium">
                                                        Reportée
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-center text-gray-400">Aucune séance</div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
    // Gestion du modal de report
    const reportModal = document.getElementById('reportModal');
    const cancelModal = document.getElementById('cancelModal');

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

        const form = document.getElementById('reportForm');
        form.action = `/coordinateur/seances/${seanceId}/reporter`;
        form.submit();
    });

    // Soumission du formulaire d'annulation
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const seanceId = document.getElementById('seance_id_cancel').value;

        const form = document.getElementById('cancelForm');
        form.action = `/coordinateur/seances/${seanceId}/annuler`;
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

    .bg-[#3d4270] {
        background-color: #f3f4f6 !important;
        color: black !important;
    }

    .text-red-400 {
        color: #dc2626 !important;
    }

    .text-yellow-400 {
        color: #d97706 !important;
    }
}
</style>
@endsection
