@extends('layouts.coordinateur')

@section('title', 'Éditer un emploi du temps')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-800 mb-6">
                    Modifier l'emploi du temps de la classe : {{ $classe->nom_classe }}
                </h1>
                <form action="{{ route('coordinateur.emploiDuTemps.update', $classe->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- Affiche les séances existantes et permet de les modifier -->
                    <div class="mb-4">
                        <label class="block font-semibold mb-2">Séances existantes :</label>
                        <ul>
                        @foreach($seances as $seance)
                            <li class="mb-2">
                                {{ $seance->date_seance }} - {{ $seance->heure_debut }} à {{ $seance->heure_fin }} :
                                {{ $seance->matiere->nom_matiere ?? '' }} avec {{ $seance->enseignant->user->nom ?? '' }}
                                ({{ $seance->typeCours->nom_type_cours ?? '' }})
                                <!-- Ici, tu peux ajouter des champs pour modifier chaque séance -->
                            </li>
                        @endforeach
                        </ul>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 