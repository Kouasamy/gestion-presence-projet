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
                        <table class="custom-table w-full coordinateur-etudiants-table">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Classe(s)</th>
                                    <th>Taux de présence</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($etudiants as $etudiant)
                                    <tr>
                                        <td data-label="Photo" class="text-center">
                                            <img src="{{ $etudiant->user->photo_url }}"
                                                 alt="Photo de {{ $etudiant->user->nom }}"
                                                 class="student-photo mx-auto">
                                        </td>
                                        <td data-label="Nom">
                                            <div class="font-medium">{{ $etudiant->user->nom }}</div>
                                        </td>
                                        <td data-label="Email">{{ $etudiant->user->email }}</td>
                                        <td data-label="Classes" class="class-list">
                                            @if($etudiant->classes && $etudiant->classes->count())
                                                <div class="space-y-2">
                                                @foreach($etudiant->classes as $classe)
                                                    <div class="class-item">
                                                        <span>{{ $classe->nom_classe }}</span>
                                                        <form method="POST" action="{{ route('coordinateur.etudiants.desinscrireClasse', ['etudiant' => $etudiant->id, 'classe' => $classe->id]) }}"
                                                              onsubmit="return confirm('Confirmer la désinscription de cette classe ?');">
                                                            @csrf
                                                            <button type="submit" class="remove-class-btn" title="Désinscrire">&times;</button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400">Aucune classe</span>
                                            @endif
                                        </td>
                                        <td data-label="Taux de présence" class="text-center">
                                            @php
                                                $taux = $etudiant->taux_presence ?? 0;
                                                $class = '';
                                                if ($taux >= 70) {
                                                    $class = 'presence-high';
                                                } elseif ($taux >= 30) {
                                                    $class = 'presence-medium';
                                                } else {
                                                    $class = 'presence-low';
                                                }
                                            @endphp
                                            <span class="presence-rate {{ $class }} inline-block">
                                                {{ $taux }}%
                                            </span>
                                        </td>
                                        <td data-label="Actions" class="text-center">
                                            <div class="flex justify-center">
                                                <a href="{{ route('coordinateur.etudiants.formAssignerClasse', $etudiant->id) }}"
                                                   class="px-4 py-2 bg-[#202149] hover:bg-[#2c2d68] text-white rounded-lg transition-all flex items-center justify-center gap-2 w-full text-center">
                                                    <i class="fas fa-plus-circle"></i>
                                                    <span>Assigner classe</span>
                                                </a>
                                            </div>
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
    if (row) {
        if (row.style.display === 'none') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// Amélioration de l'expérience mobile
document.addEventListener('DOMContentLoaded', function() {
    // S'assurer que les photos s'affichent correctement
    const studentPhotos = document.querySelectorAll('.student-photo');
    studentPhotos.forEach(photo => {
        photo.addEventListener('error', function() {
            this.src = 'https://ui-avatars.com/api/?name=Etudiant&background=random&color=fff';
        });
    });

    // Améliorer l'affichage des taux de présence sur mobile
    const presenceRates = document.querySelectorAll('.presence-rate');
    presenceRates.forEach(rate => {
        rate.classList.add('inline-block');
    });
});
</script>
@endsection
