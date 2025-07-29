@extends('layouts.parent')

@section('title', 'Tableau de bord')

@section('content')
    <div class="container mx-auto p-4 animate-fadeIn">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                👋 Bonjour, <span class="text-[#202149]">{{ Auth::user()->nom }}</span>
            </h1>
            <p class="text-gray-600">Bienvenue sur votre espace personnalisé.</p>
        </header>

        {{-- Notifications --}}
        @if ($notifications->isNotEmpty())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-md rounded-lg animate-slideIn">
                <div class="flex">
                    <div class="py-1"><i class="fa-solid fa-bell text-xl mr-3"></i></div>
                    <div>
                        <p class="font-bold">Notifications importantes</p>
                        <ul class="list-disc list-inside mt-1">
                            @foreach ($notifications as $notif)
                                <li>{{ $notif['nom'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Cartes de navigation --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            {{-- Carte Emploi du temps --}}
            <a href="{{ route('parent.emploiDuTemps') }}" class="card-placeholder group">
                <div class="text-center">
                    <i class="fa-solid fa-calendar-days card-icon text-6xl mb-2"></i>
                    <h3 class="card-label text-xl">Emploi du Temps</h3>
                    <p class="text-gray-300 text-sm">Consulter l'emploi du temps de votre enfant.</p>
                </div>
            </a>

            {{-- Carte Absences --}}
            <a href="{{ route('parent.absences') }}" class="card-placeholder group">
                <div class="text-center">
                    <i class="fa-solid fa-user-clock card-icon text-6xl mb-2"></i>
                    <h3 class="card-label text-xl">Suivi des Absences</h3>
                    <p class="text-gray-300 text-sm">Voir les absences justifiées et non justifiées.</p>
                </div>
            </a>
        </div>

        {{-- Informations sur l'étudiant et statistiques --}}
        @if ($etudiants->isNotEmpty())
            @foreach ($etudiants as $etudiant)
                <div class="bg-white shadow-lg rounded-xl p-6 border-t-4 border-[#202149] mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations sur l'Étudiant</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Détails de l'étudiant --}}
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-[#202149] mb-2 flex items-center">
                                <i class="fa-solid fa-user-graduate mr-2"></i>
                                Détails
                            </h3>
                            <p><strong>Nom :</strong> {{ $etudiant->user->nom }}</p>
                            <p><strong>Classe(s) :</strong> {{ $etudiant->classes->pluck('nom_classe')->join(', ') }}</p>
                        </div>

                        {{-- Statistiques d'assiduité --}}
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-[#202149] mb-2 flex items-center">
                                <i class="fa-solid fa-chart-pie mr-2"></i>
                                Statistiques d'Assiduité
                            </h3>
                            <div class="space-y-2">
                                <p><strong>Taux de présence :</strong>
                                    <span
                                        class="font-bold @if ($tauxPresence < 50) text-red-500 @elseif($tauxPresence < 75) text-yellow-500 @else text-green-500 @endif">
                                        {{ number_format($tauxPresence, 1) }}%
                                    </span>
                                </p>
                                <p><strong>Total des absences :</strong> <span class="font-bold">{{ $totalAbsences }}</span></p>
                                <p class="text-green-600"><strong>Justifiées :</strong> <span
                                        class="font-bold">{{ $justifiees }}</span></p>
                                <p class="text-red-600"><strong>Non justifiées :</strong> <span
                                        class="font-bold">{{ $nonJustifiees }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                <p class="font-bold">Information</p>
                <p>Aucun étudiant n'est actuellement associé à votre compte. Veuillez contacter l'administration.</p>
            </div>
        @endif
    </div>
@endsection
