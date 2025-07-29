@extends('layouts.coordinateur')

@section('title', 'Modifier la séance')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-[#2f3357] rounded-lg p-6 text-white max-w-3xl mx-auto">
            <h2 class="text-xl font-semibold mb-6">
                <i class="fas fa-edit mr-2"></i> Modifier la séance
            </h2>

            <form action="{{ route('coordinateur.seances.update', $seance->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Date de séance -->
                    <div>
                        <label class="block text-sm mb-1">Date</label>
                        <input type="date" name="date_seance" value="{{ old('date_seance', $seance->date_seance) }}"
                            class="form-input w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50" required>
                    </div>

                    <!-- Heure de début -->
                    <div>
                        <label class="block text-sm mb-1">Heure de début</label>
                        <input type="time" name="heure_debut" value="{{ old('heure_debut', $seance->heure_debut) }}"
                            class="form-input w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50" required>
                    </div>

                    <!-- Heure de fin -->
                    <div>
                        <label class="block text-sm mb-1">Heure de fin</label>
                        <input type="time" name="heure_fin" value="{{ old('heure_fin', $seance->heure_fin) }}"
                            class="form-input w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50" required>
                    </div>

                    <!-- Classe -->
                    <div>
                        <label class="block text-sm mb-1">Classe</label>
                        <select name="classe_id" class="form-select w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50" required>
                            @foreach ($classes as $classe)
                                <option value="{{ $classe->id }}"
                                    {{ $seance->classe_id == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom_classe }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Matière -->
                    <div>
                        <label class="block text-sm mb-1">Matière</label>
                        <select name="matiere_id" class="form-select w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50" required>
                            @foreach ($matieres as $matiere)
                                <option value="{{ $matiere->id }}"
                                    {{ $seance->matiere_id == $matiere->id ? 'selected' : '' }}>
                                    {{ $matiere->nom_matiere }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Enseignant -->
                    <div>
                        <label class="block text-sm mb-1">Enseignant</label>
                        <select name="enseignant_id" class="form-select w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50" required>
                            @foreach ($enseignants as $enseignant)
                                <option value="{{ $enseignant->id }}"
                                    {{ $seance->enseignant_id == $enseignant->id ? 'selected' : '' }}>
                                    {{ $enseignant->user->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type de cours -->
                    <div>
                        <label class="block text-sm mb-1">Type de cours</label>
                        <select name="type_cours_id" class="form-select w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50" required>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}"
                                    {{ $seance->type_cours_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->nom_type_cours }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Statut -->
                    <div>
                        <label class="block text-sm mb-1">Statut</label>
                        <select name="statut_seance_id" class="form-select w-full bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-[#E61845] focus:ring focus:ring-[#E61845] focus:ring-opacity-50" required>
                            @foreach ($statutsSeance as $statut)
                                <option value="{{ $statut->id }}"
                                        {{ $seance->statut_seance_id == $statut->id ? 'selected' : '' }}>
                                    {{ $statut->nom_seance }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <a href="{{ route('coordinateur.seances.index') }}" class="px-4 py-2 rounded-lg text-white">
                        Annuler
                    </a>
                    <button type="submit" class="px-4 py-2 rounded-lg text-white hover:bg-red-700"
                        style="background-color: #E61845;">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
