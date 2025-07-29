@extends('layouts.coordinateur')

@section('title', 'Assigner à une classe')

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    Assigner {{ $etudiant->user->nom }} à une classe
                </h2>
                <form method="POST" action="{{ route('coordinateur.etudiants.assignerClasse', $etudiant->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="classe_id" class="block font-medium">Classe</label>
                        <select name="classe_id" id="classe_id" class="form-input w-full" required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->nom_classe }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="annee_academique_id" class="block font-medium">Année académique</label>
                        <select name="annee_academique_id" id="annee_academique_id" class="form-input w-full" required>
                            <option value="">Sélectionner une année</option>
                            @foreach($annees as $annee)
                                <option value="{{ $annee->id }}">{{ $annee->annee }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="date_debut" class="block font-medium">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" class="form-input w-full" required>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="custom-button">Valider</button>
                        <a href="{{ route('coordinateur.etudiants.index') }}" class="custom-button bg-gray-400">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 