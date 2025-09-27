@extends('layouts.coordinateur')

@section('title', 'Liste des séances')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-[#2f3357] rounded-lg p-6 text-white">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">
                    <i class="fas fa-calendar-days mr-2"></i>Liste des séances
                </h2>

                <!-- Ajout d'un bouton de recherche/filtre -->
                <div class="flex space-x-2">
                    <button id="showFilterBtn" class="bg-[#e11d48] hover:bg-[#be123c] text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                </div>
            </div>

            <!-- Formulaire de filtrage (caché par défaut) -->
            <div id="filterForm" class="bg-[#262944] p-4 rounded-lg mb-6 hidden">
                <form action="{{ route('coordinateur.seances.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-medium mb-1">Date</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="w-full rounded-md border-gray-300 text-gray-900">
                    </div>
                    <div>
                        <label for="classe" class="block text-sm font-medium mb-1">Classe</label>
                        <select name="classe" id="classe" class="w-full rounded-md border-gray-300 text-gray-900">
                            <option value="">Toutes les classes</option>
                            @foreach(\App\Models\Classe::orderBy('nom_classe')->get() as $classe)
                                <option value="{{ $classe->id }}" {{ request('classe') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom_classe }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium mb-1">Type de cours</label>
                        <select name="type" id="type" class="w-full rounded-md border-gray-300 text-gray-900">
                            <option value="">Tous les types</option>
                            @foreach(\App\Models\TypeCours::orderBy('nom_type_cours')->get() as $typeCours)
                                <option value="{{ $typeCours->nom_type_cours }}" {{ request('type') == $typeCours->nom_type_cours ? 'selected' : '' }}>
                                    {{ $typeCours->nom_type_cours }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-[#e11d48] hover:bg-[#be123c] text-white px-4 py-2 rounded-md">
                            Appliquer
                        </button>
                        <button type="button" id="resetFilterBtn" class="ml-2 bg-[#3d4270] hover:bg-[#262944] text-white px-4 py-2 rounded-md">
                            Réinitialiser
                        </button>
                    </div>
                </form>
            </div>

            @if (session('success'))
                <div class="bg-green-600 text-white px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full">
                    <style>
                        .seance-table th,
                        .seance-table td {
                            padding: 18px 25px !important;
                        }
                        .seance-table tbody tr {
                            height: 80px;
                            transition: background-color 0.2s ease;
                        }
                        .seance-table tbody tr:hover {
                            background-color: rgba(38, 41, 68, 0.7) !important;
                        }
                        .seance-table th {
                            font-weight: 600;
                            text-transform: uppercase;
                            font-size: 0.85rem;
                            letter-spacing: 0.05em;
                        }
                        .seance-badge {
                            display: inline-block;
                            padding: 6px 12px;
                            border-radius: 9999px;
                            font-weight: 500;
                            font-size: 0.85rem;
                            text-align: center;
                            min-width: 100px;
                        }
                        .action-button {
                            padding: 8px;
                            border-radius: 9999px;
                            transition: all 0.2s ease;
                        }
                        .action-button:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        }
                    </style>
                    <thead>
                        <tr class="bg-[#262944]">
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Classe</th>
                            <th class="px-4 py-3 text-left">Matière</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#3d4270] seance-table">
                        @forelse($seances as $seance)
                            <tr class="hover:bg-[#262944] transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-300">{{ \Carbon\Carbon::parse($seance->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($seance->heure_fin)->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $seance->classe->nom_classe }}</td>
                                <td class="px-4 py-3">
                                    <div>{{ $seance->matiere->nom_matiere }}</div>
                                    <div class="text-sm text-gray-300">{{ $seance->enseignant->user->nom }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="seance-badge bg-[#3d4270]">
                                        {{ $seance->typeCours->nom_type_cours }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center space-x-4">
                                        <a href="{{ route('coordinateur.presences.form', $seance->id) }}"
                                            class="action-button bg-blue-500 text-white" title="Gérer les présences">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        <a href="{{ route('coordinateur.seances.edit', $seance->id) }}"
                                            class="action-button bg-yellow-500 text-white" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" onclick="openReportModal({{ $seance->id }})"
                                            class="action-button bg-purple-500 text-white" title="Reporter">
                                            <i class="fas fa-calendar-plus"></i>
                                        </button>
                                        <form action="{{ route('coordinateur.seances.destroy', $seance->id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette séance ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-button bg-red-500 text-white"
                                                title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-400">
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

    <!-- Modal Report -->
    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Reporter la séance</h2>
            <form id="reportForm" action="" method="POST" class="space-y-4">
                @csrf
                @method('POST')
                <input type="hidden" name="seance_id" id="seance_id_report">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle date</label>
                    <input type="date" name="nouvelle_date" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle heure de début</label>
                    <input type="time" name="heure_debut" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouvelle heure de fin</label>
                    <input type="time" name="heure_fin" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif du report</label>
                    <textarea name="motif" class="w-full border-gray-300 rounded-md shadow-sm" rows="3" required></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#e11d48] text-white rounded-lg hover:bg-[#be123c]">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('after_content')
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showFilterBtn = document.getElementById('showFilterBtn');
            const filterForm = document.getElementById('filterForm');
            const resetFilterBtn = document.getElementById('resetFilterBtn');
            const reportModal = document.getElementById('reportModal');

            showFilterBtn.addEventListener('click', function() {
                filterForm.classList.toggle('hidden');
            });

            resetFilterBtn.addEventListener('click', function() {
                window.location.href = "{{ route('coordinateur.seances.index') }}";
            });

            // Gestion du modal de report
            window.openReportModal = function(seanceId) {
                document.getElementById('seance_id_report').value = seanceId;
                reportModal.classList.remove('hidden');
                reportModal.classList.add('flex');
            }

            window.closeReportModal = function() {
                reportModal.classList.add('hidden');
                reportModal.classList.remove('flex');
                document.getElementById('reportForm').reset();
            }

            // Soumission du formulaire de report
            document.getElementById('reportForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const seanceId = document.getElementById('seance_id_report').value;

                const form = document.getElementById('reportForm');
                form.action = `/coordinateur/seances/${seanceId}/reporter`;
                form.submit();
            });

            // Fermer le modal si on clique en dehors
            window.onclick = function(event) {
                if (event.target === reportModal) {
                    closeReportModal();
                }
            }
        });
    </script>
    @endpush
@endsection
