@extends('layouts.coordinateur')

@section('title', 'Liste des séances')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-[#2f3357] rounded-lg p-6 text-white">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">
                    <i class="fas fa-calendar-days mr-2"></i>Liste des séances
                </h2>
            </div>

            @if (session('success'))
                <div class="bg-green-600 text-white px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[#262944]">
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Horaires</th>
                            <th class="px-4 py-3 text-left">Classe</th>
                            <th class="px-4 py-3 text-left">Matière</th>
                            <th class="px-4 py-3 text-left">Enseignant</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-left">Statut</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#3d4270]">
                        @forelse($seances as $seance)
                            <tr class="hover:bg-[#262944] transition-colors">
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($seance->heure_debut)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($seance->heure_fin)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3">{{ $seance->classe->nom_classe }}</td>
                                <td class="px-4 py-3">{{ $seance->matiere->nom_matiere }}</td>
                                <td class="px-4 py-3">{{ $seance->enseignant->user->nom }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-[#3d4270] rounded-full text-sm">
                                        {{ $seance->typeCours->nom_type_cours }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-[#3d4270] rounded-full text-sm">
                                        {{ $seance->statutSeance->nom_seance }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('coordinateur.presences.form', $seance->id) }}"
                                            class="text-blue-400 hover:text-blue-300" title="Gérer les présences">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        <a href="{{ route('coordinateur.seances.edit', $seance->id) }}"
                                            class="text-yellow-400 hover:text-yellow-300" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('coordinateur.seances.destroy', $seance->id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette séance ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300"
                                                title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-center text-gray-400">
                                    Aucune séance trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $seances->links() }}
            </div>
        </div>
    </div>
@endsection




@section('after_content')


    @push('scripts')


    @endpush
@endsection
