@extends('layouts.coordinateur')

@section('title', 'Créer une séance')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-[#2f3357] rounded-lg p-6 text-white max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">
                <i class="fas fa-plus-circle mr-2"></i>Créer une nouvelle séance
            </h2>
            <a href="{{ route('coordinateur.seances.index') }}"
               class="px-4 py-2 bg-gray-600 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <form action="{{ route('coordinateur.seances.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Classe -->
                <div>
                    <label for="classe_id" class="block text-sm font-medium mb-2">Classe</label>
                    <select name="classe_id" id="classe_id" required
                            class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50">
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom_classe }}
                            </option>
                        @endforeach
                    </select>
                    @error('classe_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Matière -->
                <div>
                    <label for="matiere_id" class="block text-sm font-medium mb-2">Matière</label>
                    <select name="matiere_id" id="matiere_id" required
                            class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50">
                        <option value="">Sélectionner une matière</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom_matiere }}
                            </option>
                        @endforeach
                    </select>
                    @error('matiere_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Enseignant -->
                <div>
                    <label for="enseignant_id" class="block text-sm font-medium mb-2">Enseignant</label>
                    <select name="enseignant_id" id="enseignant_id" required
                            class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50">
                        <option value="">Sélectionner un enseignant</option>
                        @foreach($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}" {{ old('enseignant_id') == $enseignant->id ? 'selected' : '' }}>
                                {{ $enseignant->user->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('enseignant_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type de cours -->
                <div>
                    <label for="type_cours_id" class="block text-sm font-medium mb-2">Type de cours</label>
                    <select name="type_cours_id" id="type_cours_id" required
                            class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50">
                        <option value="">Sélectionner un type</option>
                        @foreach($typesCours as $type)
                            <option value="{{ $type->id }}" {{ old('type_cours_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->nom_type_cours }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_cours_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="date_seance" class="block text-sm font-medium mb-2">Date</label>
                    <input type="date" name="date_seance" id="date_seance" required
                           value="{{ old('date_seance') }}"
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50">
                    @error('date_seance')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="statut_seance_id" class="block text-sm font-medium mb-2">Statut</label>
                    <select name="statut_seance_id" id="statut_seance_id" required
                            class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50">
                        <option value="">Sélectionner un statut</option>
                        @foreach($statutsSeance as $statut)
                            <option value="{{ $statut->id }}" {{ old('statut_seance_id') == $statut->id ? 'selected' : '' }}>
                                {{ $statut->nom_statut_seance }}
                            </option>
                        @endforeach
                    </select>
                    @error('statut_seance_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Heure début -->
                <div>
                    <label for="heure_debut" class="block text-sm font-medium mb-2">Heure de début</label>
                    <input type="time" name="heure_debut" id="heure_debut" required
                           value="{{ old('heure_debut') }}"
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50">
                    @error('heure_debut')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Heure fin -->
                <div>
                    <label for="heure_fin" class="block text-sm font-medium mb-2">Heure de fin</label>
                    <input type="time" name="heure_fin" id="heure_fin" required
                           value="{{ old('heure_fin') }}"
                           class="w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50">
                    @error('heure_fin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit"
                        class="px-6 py-3 bg-[#e11d48] text-white rounded-lg hover:bg-[#be123c] transition-colors">
                    <i class="fas fa-save mr-2"></i>Créer la séance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
