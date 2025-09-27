@extends('layouts.enseignant')

@section('title', 'Mon emploi du temps')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-calendar-alt mr-2"></i>Mon emploi du temps
                </h2>

                <div class="flex space-x-2">
                    <a href="{{ route('enseignant.emploiDuTemps', ['type' => 'tous']) }}"
                       class="px-4 py-2 rounded-lg {{ $type === 'tous' ? 'bg-[#e11d48] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Tous
                    </a>
                    <a href="{{ route('enseignant.emploiDuTemps', ['type' => 'avenir']) }}"
                       class="px-4 py-2 rounded-lg {{ $type === 'avenir' ? 'bg-[#e11d48] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        À venir
                    </a>
                    <a href="{{ route('enseignant.emploiDuTemps', ['type' => 'passé']) }}"
                       class="px-4 py-2 rounded-lg {{ $type === 'passé' ? 'bg-[#e11d48] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Passés
                    </a>
                </div>
            </div>

            @if(empty($emploiDuTemps))
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-500">Aucune séance programmée pour le moment.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">Période</th>
                                @foreach($jours as $jour)
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $jour }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Matin -->
                            <tr class="border-b">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r bg-gray-50">
                                    <div class="font-bold">Matin</div>
                                    <div class="text-xs text-gray-500">09h00 - 12h00</div>
                                </td>

                                @foreach($jours as $jour)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r align-top">
                                        @if(isset($emploiDuTemps[$jour]['matin']))
                                            <div class="p-2 rounded-lg
                                                @if($emploiDuTemps[$jour]['matin']['est_passee']) bg-gray-100
                                                @elseif($emploiDuTemps[$jour]['matin']['est_courante']) bg-green-100
                                                @else bg-blue-100 @endif">

                                                <div class="font-semibold text-gray-800">{{ $emploiDuTemps[$jour]['matin']['cours'] }}</div>
                                                <div class="text-xs text-gray-600">{{ $emploiDuTemps[$jour]['matin']['classe'] }}</div>
                                                <div class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($emploiDuTemps[$jour]['matin']['heure_debut'])->format('H:i') }} - {{ \Carbon\Carbon::parse($emploiDuTemps[$jour]['matin']['heure_fin'])->format('H:i') }}</div>
                                                <div class="text-xs text-gray-600">{{ $emploiDuTemps[$jour]['matin']['type'] }}</div>

                                                <div class="mt-2 flex justify-between items-center">
                                                    <span class="text-xs px-2 py-1 rounded-full
                                                        @if($emploiDuTemps[$jour]['matin']['est_passee']) bg-gray-200 text-gray-800
                                                        @elseif($emploiDuTemps[$jour]['matin']['est_courante']) bg-green-200 text-green-800
                                                        @else bg-blue-200 text-blue-800 @endif">
                                                        {{ $emploiDuTemps[$jour]['matin']['est_passee'] ? 'Passée' : ($emploiDuTemps[$jour]['matin']['est_courante'] ? 'En cours' : 'À venir') }}
                                                    </span>

                                                    @if(($emploiDuTemps[$jour]['matin']['est_passee'] || $emploiDuTemps[$jour]['matin']['est_courante']) && strtolower($emploiDuTemps[$jour]['matin']['type']) !== 'workshop' && strtolower($emploiDuTemps[$jour]['matin']['type']) !== 'e-learning')
                                                        <a href="{{ route('enseignant.formulairePresence', $emploiDuTemps[$jour]['matin']['id']) }}"
                                                           class="text-xs px-2 py-1 bg-[#e11d48] text-white rounded hover:bg-[#be123c] transition-colors"
                                                           onclick="event.preventDefault(); window.location.href='{{ route('enseignant.formulairePresence', $emploiDuTemps[$jour]['matin']['id']) }}';">
                                                            <i class="fas fa-clipboard-check mr-1"></i>Saisir présences
                                                        </a>
                                                    @elseif(($emploiDuTemps[$jour]['matin']['est_passee'] || $emploiDuTemps[$jour]['matin']['est_courante']) && (strtolower($emploiDuTemps[$jour]['matin']['type']) === 'workshop' || strtolower($emploiDuTemps[$jour]['matin']['type']) === 'e-learning'))
                                                        <span class="text-xs text-gray-500">
                                                            <i class="fas fa-info-circle mr-1"></i>Pas de présences pour ce type
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Après-midi -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r bg-gray-50">
                                    <div class="font-bold">Après-midi</div>
                                    <div class="text-xs text-gray-500">13h30 - 16h30</div>
                                </td>

                                @foreach($jours as $jour)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r align-top">
                                        @if(isset($emploiDuTemps[$jour]['soir']))
                                            <div class="p-2 rounded-lg
                                                @if($emploiDuTemps[$jour]['soir']['est_passee']) bg-gray-100
                                                @elseif($emploiDuTemps[$jour]['soir']['est_courante']) bg-green-100
                                                @else bg-blue-100 @endif">

                                                <div class="font-semibold text-gray-800">{{ $emploiDuTemps[$jour]['soir']['cours'] }}</div>
                                                <div class="text-xs text-gray-600">{{ $emploiDuTemps[$jour]['soir']['classe'] }}</div>
                                                <div class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($emploiDuTemps[$jour]['soir']['heure_debut'])->format('H:i') }} - {{ \Carbon\Carbon::parse($emploiDuTemps[$jour]['soir']['heure_fin'])->format('H:i') }}</div>
                                                <div class="text-xs text-gray-600">{{ $emploiDuTemps[$jour]['soir']['type'] }}</div>

                                                <div class="mt-2 flex justify-between items-center">
                                                    <span class="text-xs px-2 py-1 rounded-full
                                                        @if($emploiDuTemps[$jour]['soir']['est_passee']) bg-gray-200 text-gray-800
                                                        @elseif($emploiDuTemps[$jour]['soir']['est_courante']) bg-green-200 text-green-800
                                                        @else bg-blue-200 text-blue-800 @endif">
                                                        {{ $emploiDuTemps[$jour]['soir']['est_passee'] ? 'Passée' : ($emploiDuTemps[$jour]['soir']['est_courante'] ? 'En cours' : 'À venir') }}
                                                    </span>

                                                    @if(($emploiDuTemps[$jour]['soir']['est_passee'] || $emploiDuTemps[$jour]['soir']['est_courante']) && strtolower($emploiDuTemps[$jour]['soir']['type']) !== 'workshop' && strtolower($emploiDuTemps[$jour]['soir']['type']) !== 'e-learning')
                                                        <a href="{{ route('enseignant.formulairePresence', $emploiDuTemps[$jour]['soir']['id']) }}"
                                                           class="text-xs px-2 py-1 bg-[#e11d48] text-white rounded hover:bg-[#be123c] transition-colors"
                                                           onclick="event.preventDefault(); window.location.href='{{ route('enseignant.formulairePresence', $emploiDuTemps[$jour]['soir']['id']) }}';">
                                                            <i class="fas fa-clipboard-check mr-1"></i>Saisir présences
                                                        </a>
                                                    @elseif(($emploiDuTemps[$jour]['soir']['est_passee'] || $emploiDuTemps[$jour]['soir']['est_courante']) && (strtolower($emploiDuTemps[$jour]['soir']['type']) === 'workshop' || strtolower($emploiDuTemps[$jour]['soir']['type']) === 'e-learning'))
                                                        <span class="text-xs text-gray-500">
                                                            <i class="fas fa-info-circle mr-1"></i>Pas de présences pour ce type
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex space-x-4">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-gray-400 mr-2"></span>
                        <span class="text-sm text-gray-600">Passée</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                        <span class="text-sm text-gray-600">En cours</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                        <span class="text-sm text-gray-600">À venir</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
