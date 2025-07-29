@extends('layouts.coordinateur')

@section('title', 'Saisie des présences')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Saisie des présences</h2>
            <a href="{{ route('coordinateur.seances.index') }}" class="text-white opacity-80 hover:opacity-100">
                Retour à la liste
                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <!-- Informations de la séance -->
        <div class="bg-gray-800 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4 text-white">
                <div>
                    <p class="text-gray-400">Classe</p>
                    <p class="font-semibold">{{ $seance->classe->nom_classe }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Matière</p>
                    <p class="font-semibold">{{ $seance->matiere->nom_matiere }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Date</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400">Horaires</p>
                    <p class="font-semibold">
                        {{ \Carbon\Carbon::parse($seance->heure_debut)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($seance->heure_fin)->format('H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-400">Type de cours</p>
                    <p class="font-semibold">{{ $seance->typeCours->nom_type_cours }}</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="form-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="form-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('coordinateur.presences.store', $seance->id) }}" method="POST" class="space-y-6">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-white">
                    <thead>
                        <tr class="bg-gray-800">
                            <th class="px-4 py-2 text-left">Photo</th>
                            <th class="px-4 py-2 text-left">Étudiant</th>
                            <th class="px-4 py-2 text-left">Statut</th>
                            <th class="px-4 py-2 text-left">Justification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($seance->classe->etudiants as $etudiant)
                            <tr class="border-b border-gray-700 hover:bg-gray-800 transition-colors">
                                <td class="px-4 py-3">
                                    <img src="{{ $etudiant->user->photo_url ?? 'https://randomuser.me/api/portraits/men/' . ($etudiant->id % 100) . '.jpg' }}"
                                         alt="Photo de {{ $etudiant->user->nom }}"
                                         class="w-10 h-10 rounded-full object-cover">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $etudiant->user->nom }}</span>
                                        <span class="text-sm text-gray-400">{{ $etudiant->numero_etudiant }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <select name="presences[{{ $etudiant->id }}][statut_presence_id]"
                                            class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50"
                                            required
                                            onchange="updateSelectColor(this)">
                                        @foreach($statutsPresence as $statut)
                                            <option value="{{ $statut->id }}"
                                                {{ old("presences.{$etudiant->id}.statut_presence_id",
                                                    isset($presences[$etudiant->id]) ? $presences[$etudiant->id]->statut_presence_id : '') == $statut->id ? 'selected' : '' }}>
                                                {{ $statut->nom_statut_presence }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text"
                                           name="presences[{{ $etudiant->id }}][justification]"
                                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50"
                                           placeholder="Justification (optionnelle)"
                                           value="{{ old("presences.{$etudiant->id}.justification",
                                                isset($presences[$etudiant->id]) ? $presences[$etudiant->id]->justification : '') }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="form-button bg-red-600 hover:bg-red-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les présences
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateSelectColor(select) {
    // Réinitialiser les classes
    select.classList.remove('border-red-500', 'border-green-500', 'border-yellow-500', 'ring-red-500', 'ring-green-500', 'ring-yellow-500');

    // Appliquer les nouvelles classes selon le statut
    if (select.value == '2') {
        select.classList.add('border-red-500');
        select.style.borderWidth = '2px';
    } else if (select.value == '1') {
        select.classList.add('border-green-500');
        select.style.borderWidth = '2px';
    } else if (select.value == '3') {
        select.classList.add('border-yellow-500');
        select.style.borderWidth = '2px';
    } else {
        select.style.borderWidth = '1px';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les couleurs des selects au chargement
    document.querySelectorAll('select[name^="presences"]').forEach(select => {
        updateSelectColor(select);
    });
});
</script>
@endpush
@endsection
