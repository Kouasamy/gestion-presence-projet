@extends('layouts.parent')

@section('title', 'Emploi du Temps')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 relative">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h1 class="text-xl font-semibold text-gray-800 mb-2">
                    Emploi du temps de vos enfants
                </h1>
                <p class="text-gray-600 mb-6">Semaine du {{ $debutSemaine }} au {{ $finSemaine }}</p>

                @forelse($etudiantsEmploiDuTemps as $etudiantData)
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            {{ $etudiantData['etudiant']->user->nom ?? 'N/A' }}
                        </h2>

                        @forelse($etudiantData['emploiDuTemps'] as $classeData)
                            <div class="mb-6">
                                <h3 class="text-md font-semibold text-gray-700 mb-2">
                                    Classe : {{ $classeData['classe']->nom_classe ?? 'N/A' }}
                                </h3>

                                <!-- Emploi du temps -->
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse bg-[#2f3357] text-white rounded-lg overflow-hidden mb-6 text-lg" style="min-width:1200px;">
                                        <thead>
                                            <tr>
                                                <th class="p-6 bg-[#262944] font-semibold text-xl">Horaires</th>
                                                @foreach($jours as $jour)
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
                                                    @foreach($jours as $jour)
                                                        <td class="p-6 border border-[#3d4270] text-lg h-20 align-middle">
                                                            @if(isset($classeData['emploiDuTemps'][$jour][$periode]))
                                                                <div class="bg-[#3d4270] rounded-lg p-4">
                                                                    <div class="font-semibold">{{ $classeData['emploiDuTemps'][$jour][$periode]['cours'] }}</div>
                                                                    <div class="text-sm opacity-80">{{ $classeData['emploiDuTemps'][$jour][$periode]['enseignant'] }}</div>
                                                                    <div class="text-sm">{{ $classeData['emploiDuTemps'][$jour][$periode]['type'] }}</div>
                                                                    <div class="text-sm mt-2">
                                                                        {{ \Carbon\Carbon::parse($classeData['emploiDuTemps'][$jour][$periode]['heure_debut'])->format('H:i') }} -
                                                                        {{ \Carbon\Carbon::parse($classeData['emploiDuTemps'][$jour][$periode]['heure_fin'])->format('H:i') }}
                                                                    </div>
                                                                    @if($classeData['emploiDuTemps'][$jour][$periode]['statut_id'] == 3)
                                                                        <div class="mt-2 text-red-400 font-medium">
                                                                            Annulée
                                                                        </div>
                                                                    @elseif($classeData['emploiDuTemps'][$jour][$periode]['statut_id'] == 4)
                                                                        <div class="mt-2 text-yellow-400 font-medium">
                                                                            Reportée
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <div class="text-center text-gray-400">Aucune séance</div>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600">Aucune classe assignée à cet enfant.</p>
                        @endforelse
                    </div>
                @empty
                    <p class="text-gray-600">Aucun enfant associé trouvé.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .nav-menu,
    .header-icons,
    button {
        display: none !important;
    }

    .max-w-7xl {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .shadow-sm {
        box-shadow: none !important;
    }

    .bg-[#2f3357] {
        background-color: white !important;
        color: black !important;
    }

    .bg-[#262944] {
        background-color: white !important;
        color: black !important;
        border: 1px solid #ddd !important;
    }

    .border-[#3d4270] {
        border-color: #ddd !important;
    }

    .text-white {
        color: black !important;
    }

    .bg-[#3d4270] {
        background-color: #f3f4f6 !important;
        color: black !important;
    }

    .text-red-400 {
        color: #dc2626 !important;
    }

    .text-yellow-400 {
        color: #d97706 !important;
    }
}
</style>
@endsection
