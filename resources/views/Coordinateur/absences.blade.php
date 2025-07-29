@extends('layouts.coordinateur')

@section('title', 'Gestion des absences')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-user-clock mr-2"></i>Gestion des absences
                    </h2>
                </div>

                <!-- Filtres -->
                <form method="GET" action="{{ route('coordinateur.absences.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <select name="classe" class="form-input">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ request('classe') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom_classe }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="form-input">
                        <option value="">Tous les statuts</option>
                        <option value="non_justifiee" {{ request('status') == 'non_justifiee' ? 'selected' : '' }}>Non justifiées</option>
                        <option value="justifiee" {{ request('status') == 'justifiee' ? 'selected' : '' }}>Justifiées</option>
                    </select>

                    <div class="md:col-span-4">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            Filtrer
                        </button>
                    </div>
                </form>

                @if($absences->isEmpty())
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-check text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500">Aucune absence trouvée pour les critères sélectionnés</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($absences as $absence)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($absence->seance->date_seance)->format('d/m/Y') }}
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($absence->seance->heure_debut)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($absence->seance->heure_fin)->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $absence->etudiant->user->nom }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ $absence->seance->classe->nom_classe }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ $absence->seance->matiere->nom_matiere }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($absence->justificationAbsence)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Justifiée le {{ \Carbon\Carbon::parse($absence->justificationAbsence->date_justification)->format('d/m/Y') }}
                                                </span>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ Str::limit($absence->justificationAbsence->motif, 30) }}
                                                </div>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Non justifiée
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium">
                                            @if(!$absence->justificationAbsence)
                                                <button onclick="openJustificationModal('{{ $absence->id }}')"
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                    Justifier
                                                </button>
                                            @else
                                                <a href="{{ route('coordinateur.absences.details', $absence->id) }}"
                                                   class="text-gray-600 hover:text-gray-900">
                                                    Voir détails
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $absences->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de justification -->
<div id="justification_modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Justifier une absence
            </h3>
            <form method="POST" id="justification_form" class="space-y-4">
                @csrf
                <div>
                    <label for="motif" class="block text-sm font-medium text-gray-700 mb-1">
                        Motif de l'absence
                    </label>
                    <textarea id="motif"
                              name="motif"
                              rows="3"
                              class="form-input w-full"
                              required></textarea>
                </div>

                <div>
                    <label for="date_justification" class="block text-sm font-medium text-gray-700 mb-1">
                        Date de justification
                    </label>
                    <input type="date"
                           id="date_justification"
                           name="date_justification"
                           class="form-input w-full"
                           value="{{ date('Y-m-d') }}"
                           required>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button"
                            onclick="closeJustificationModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openJustificationModal(absenceId) {
    const form = document.getElementById('justification_form');
    form.action = '/coordinateur/absences/' + absenceId + '/justify';
    document.getElementById('justification_modal').classList.remove('hidden');
}

function closeJustificationModal() {
    document.getElementById('justification_modal').classList.add('hidden');
    document.getElementById('justification_form').reset();
}

// Gestion des filtres
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[method="GET"]');
    if (form) {
        form.querySelectorAll('select, input[type="date"]').forEach(element => {
            element.addEventListener('change', () => form.submit());
        });
    }
});
</script>
@endpush
@endsection
