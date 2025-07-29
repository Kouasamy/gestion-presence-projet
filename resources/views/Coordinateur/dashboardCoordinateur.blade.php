@extends('layouts.coordinateur')

@section('title', 'Tableau de bord')

@section('content')
<div class="dashboard-container p-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-calendar text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-lg font-semibold text-gray-700">Séances aujourd'hui</h2>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['seancesAujourdhui'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i class="fas fa-clipboard-list text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-lg font-semibold text-gray-700">Présences à saisir</h2>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['presencesASaisir'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <i class="fas fa-user-xmark text-red-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-lg font-semibold text-gray-700">Absences non justifiées</h2>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['absencesNonJustifiees'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons d'action principaux -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('coordinateur.presences.index') }}" class="bg-red-600 hover:bg-red-700 text-white rounded-lg p-4 text-center flex items-center justify-center gap-3">
            <i class="fas fa-clipboard-user"></i>
            Gérer les présences
        </a>
        <a href="{{ route('coordinateur.emploiDuTemps.create') }}" class="bg-red-600 hover:bg-red-700 text-white rounded-lg p-4 text-center flex items-center justify-center gap-3">
            <i class="fas fa-calendar-plus"></i>
            Créer emploi du temps
        </a>
        <a href="{{ route('coordinateur.statistiques') }}" class="bg-red-600 hover:bg-red-700 text-white rounded-lg p-4 text-center flex items-center justify-center gap-3">
            <i class="fas fa-chart-line"></i>
            Voir les statistiques
        </a>
    </div>

    <!-- Alertes importantes -->
    @if($stats['etudiantsDroppes']->isNotEmpty())
    <div class="mb-8">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-triangle-exclamation text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-yellow-800">
                        Alertes importantes
                    </h3>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($stats['etudiantsDroppes'] as $etudiant)
                <div class="flex items-center space-x-3 bg-white p-3 rounded-lg shadow-sm">
                    <img src="{{ $etudiant['photo'] }}" alt="{{ $etudiant['nom'] }}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <div class="font-medium text-gray-900">{{ $etudiant['nom'] }}</div>
                        <div class="text-sm text-red-600">Taux de présence critique : {{ $etudiant['taux'] }}%</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Sections de navigation -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">
                <i class="fas fa-calendar-days mr-2"></i>
                Gestion des séances
            </h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('coordinateur.seances.index') }}" class="text-blue-600 hover:text-blue-800">
                        → Liste des séances
                    </a>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">
                <i class="fas fa-users mr-2"></i>
                Gestion des étudiants
            </h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('coordinateur.etudiants.index') }}" class="text-blue-600 hover:text-blue-800">
                        → Liste des étudiants
                    </a>
                </li>
                <li>
                    <a href="{{ route('coordinateur.absences.index') }}" class="text-blue-600 hover:text-blue-800">
                        → Gérer les absences
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
