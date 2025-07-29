@extends('layouts.coordinateur')

@section('title', 'Créer un emploi du temps')

@section('content')
<div class="py-6">
    <div class="max-w-24xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 relative">
                <a href="{{ route('coordinateur.emploiDuTemps.index') }}"
                   class="absolute top-6 right-6 text-violet-700 hover:text-violet-900 font-semibold underline transition-colors text-lg">
                    Voir l'emploi du temps
                </a>
                <h1 class="text-xl font-semibold text-gray-800 mb-6">
                    Emploi du temps - Création
                </h1>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('coordinateur.emploiDuTemps.store') }}" method="POST">
                    @csrf

                    <!-- Sélection de la classe -->
                    <div class="mb-8">
                        <label for="classe_id" class="block text-xl font-semibold text-gray-800 mb-3">Sélectionner la classe</label>
                        <select name="classe_id"
                                id="classe_id"
                                class="w-full border-2 border-gray-300 rounded-lg shadow-sm py-3 px-4 bg-white text-lg focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 transition-colors"
                                required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom_classe }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <table class="w-full border-collapse bg-[#2f3357] text-white rounded-lg overflow-hidden mb-6 text-lg" style="min-width:1200px;">
                        <thead>
                            <tr>
                                <th class="p-6 bg-[#262944] font-semibold text-xl">Horaires</th>
                                @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'] as $jour)
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
                                    @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'] as $jour)
                                        <td class="p-6 border border-[#3d4270] text-lg h-20 align-middle">
                                            <div class="space-y-2">
                                                <div class="bg-[#3d4270] rounded-lg p-4 space-y-3">
                                                    <select name="seances[{{ $jour }}_{{ $periode }}][matiere_id]"
                                                            class="w-full border-2 border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white text-gray-800 text-base focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 transition-colors">
                                                        <option value="">Sélectionner une matière</option>
                                                        @foreach($matieres as $matiere)
                                                            <option value="{{ $matiere->id }}">{{ $matiere->nom_matiere }}</option>
                                                        @endforeach
                                                    </select>

                                                    <select name="seances[{{ $jour }}_{{ $periode }}][enseignant_id]"
                                                            class="w-full border-2 border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white text-gray-800 text-base focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 transition-colors">
                                                        <option value="">Sélectionner un enseignant</option>
                                                        @foreach($enseignants as $enseignant)
                                                            <option value="{{ $enseignant->id }}">{{ $enseignant->user->nom }}</option>
                                                        @endforeach
                                                    </select>

                                                    <select name="seances[{{ $jour }}_{{ $periode }}][type_cours_id]"
                                                            class="w-full border-2 border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white text-gray-800 text-base focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 transition-colors">
                                                        <option value="">Type de cours</option>
                                                        @foreach($typesCours as $type)
                                                            <option value="{{ $type->id }}">{{ $type->nom_type_cours }}</option>
                                                        @endforeach
                                                    </select>

                                                    <input type="date"
                                                           name="seances[{{ $jour }}_{{ $periode }}][date_seance]"
                                                           class="w-full border-2 border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white text-gray-800 text-base focus:border-[#e11d48] focus:ring focus:ring-[#e11d48] focus:ring-opacity-50 transition-colors"
                                                           data-jour="{{ $jour }}">
                                                </div>

                                                <input type="hidden"
                                                       name="seances[{{ $jour }}_{{ $periode }}][periode]"
                                                       value="{{ $periode }}">
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-center">
                        <button type="submit"
                                class="px-6 py-3 bg-[#e11d48] text-white font-semibold rounded-lg hover:bg-[#be123c] transition-colors">
                            Créer l'emploi du temps
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
    // Validation de la date selon le jour sélectionné
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const expectedDay = this.dataset.jour;
            const dayMap = {
                'Lundi': 1,
                'Mardi': 2,
                'Mercredi': 3,
                'Jeudi': 4,
                'Vendredi': 5
            };

            if (selectedDate.getDay() !== dayMap[expectedDay]) {
                alert(`La date sélectionnée doit être un ${expectedDay}`);
                this.value = '';
            }
        });
    });
});
</script>
@endpush
@endsection
