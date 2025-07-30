@extends('layouts.enseignant')

@section('title', 'Tableau de bord Enseignant')

@section('content')
<div class="py-6 flex justify-center">
    <div class="max-w-4xl w-full sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-8 text-center">
            <h1 class="text-4xl font-bold text-[#202149] mb-8">Bienvenue sur votre tableau de bord</h1>
            <p class="text-lg text-gray-300 mb-12">Gérez facilement vos séances, présences et emploi du temps.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <a href="{{ route('enseignant.listeSeances') }}" class="bg-[#202149] hover:bg-[#1a1b3a] text-white rounded-lg p-8 flex flex-col items-center shadow-md transition">
                    <i class="fa-solid fa-calendar-days text-5xl mb-6"></i>
                    <span class="text-2xl font-semibold">Mes séances</span>
                    <p class="mt-3 text-center text-base text-blue-200">Consultez la liste de vos séances à venir et passées</p>
                </a>

                <a href="{{ route('enseignant.emploiDuTemps') }}" class="bg-[#e63946] hover:bg-[#d62839] text-white rounded-lg p-8 flex flex-col items-center shadow-md transition">
                    <i class="fa-solid fa-clock text-5xl mb-6"></i>
                    <span class="text-2xl font-semibold">Mon emploi du temps</span>
                    <p class="mt-3 text-center text-base text-red-100">Visualisez votre emploi du temps complet</p>
                </a>

                <a href="{{ route('enseignant.listeSeances') }}" class="bg-[#457b9d] hover:bg-[#356b8d] text-white rounded-lg p-8 flex flex-col items-center shadow-md transition">
                    <i class="fa-solid fa-user-check text-5xl mb-6"></i>
                    <span class="text-2xl font-semibold">Saisie des présences</span>
                    <p class="mt-3 text-center text-base text-indigo-200">Saisissez et modifiez les présences pour vos cours en présentiel</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
