@extends('layouts.etudiant')

@section('title', 'Mon emploi du temps')

@section('content')
<div class="py-6">
    <div class="max-w-24xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Mon emploi du temps
                    </h1>
                    <div class="flex items-center space-x-4">
                        @if(isset($classe))
                            <span class="text-lg text-gray-600">
                                Classe : {{ $classe->nom_classe }}
                            </span>
                        @endif
                    </div>
                </div>

                @if(isset($error))
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    {{ $error }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

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
                                        @if(isset($emploiDuTemps[$jour][$periode]))
                                            <div class="bg-[#3d4270] rounded-lg p-4">
                                                <div class="font-semibold">{{ $emploiDuTemps[$jour][$periode]['cours'] }}</div>
                                                <div class="text-sm opacity-80">{{ $emploiDuTemps[$jour][$periode]['enseignant'] }}</div>
                                                <div class="text-sm">{{ $emploiDuTemps[$jour][$periode]['type'] }}</div>
                                                <div class="text-sm mt-2">
                                                    {{ Carbon\Carbon::parse($emploiDuTemps[$jour][$periode]['heure_debut'])->format('H:i') }} -
                                                    {{ Carbon\Carbon::parse($emploiDuTemps[$jour][$periode]['heure_fin'])->format('H:i') }}
                                                </div>
                                                <div></div>
                                                @if($emploiDuTemps[$jour][$periode]['statut_id'] == 3)
                                                    <div class="mt-2 text-red-400 font-medium">
                                                        Annulée
                                                    </div>
                                                @elseif($emploiDuTemps[$jour][$periode]['statut_id'] == 4)
                                                    <div class="mt-2 text-yellow-400 font-medium">
                                                        Reportée
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-center text-gray-400">
                                                Aucune séance
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
