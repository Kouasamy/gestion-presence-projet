@extends('layouts.coordinateur')

@section('title', 'Liste des étudiants')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-users mr-2"></i>Liste des étudiants
                    </h2>
                    <form method="GET" action="{{ route('coordinateur.etudiants.index') }}" class="flex gap-2">
                        <select name="classe" class="form-input">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ request('classe') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom_classe }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="custom-button">Filtrer</button>
                    </form>
                </div>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($etudiants->isEmpty())
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500">Aucun étudiant trouvé.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Classe(s)</th>
                                    <th>Taux de présence</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($etudiants as $etudiant)
                                    <tr>
                                        <td>
                                            <img src="{{ $etudiant->photo_path ?? '/default-avatar.png' }}" alt="Photo" class="w-10 h-10 rounded-full object-cover">
                                        </td>
                                        <td>{{ $etudiant->user->nom }}</td>
                                        <td>{{ $etudiant->user->email }}</td>
                                        <td>
                                            @if($etudiant->classes && $etudiant->classes->count())
                                                <ul class="list-disc pl-4">
                                                @foreach($etudiant->classes as $classe)
                                                    <li>
                                                        {{ $classe->nom_classe }}
                                                        <form method="POST" action="{{ route('coordinateur.etudiants.desinscrireClasse', ['etudiant' => $etudiant->id, 'classe' => $classe->id]) }}" style="display:inline;" onsubmit="return confirm('Confirmer la désinscription de cette classe ?');">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:underline ml-2" title="Désinscrire">&times;</button>
                                                        </form>
                                                    </li>
                                                @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $etudiant->taux_presence ?? '-' }}%</td>
                                        <td>
                                            <a href="{{ route('coordinateur.etudiants.formAssignerClasse', $etudiant->id) }}" class="custom-button">Assigner à une classe</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleAssignForm(etudiantId) {
    const row = document.getElementById('assign-form-row-' + etudiantId);
    if (row.style.display === 'none') {
        row.style.display = '';
    } else {
        row.style.display = 'none';
    }
}
</script>
@endsection 