@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('header', 'Tableau de bord administrateur')

@section('content')
<main class="cards-row">
    <a href="{{ route('admin.user.index') }}" style="text-decoration: none;">
        <div class="card-placeholder">
            <i class="fa-solid fa-users card-icon"></i>
            <div class="card-label">Gestion des utilisateurs</div>
        </div>
    </a>

    <a href="{{ route('admin.cours.index') }}" style="text-decoration: none;">
        <div class="card-placeholder">
            <i class="fa-solid fa-book-open card-icon"></i>
            <div class="card-label">Gestion cours</div>
        </div>
    </a>

    <a href="{{ route('admin.classes.index') }}" style="text-decoration: none;">
        <div class="card-placeholder">
            <i class="fa-solid fa-school card-icon"></i>
            <div class="card-label">Gestion classes</div>
        </div>
    </a>

    <a href="{{ route('admin.role.index') }}" style="text-decoration: none;">
        <div class="card-placeholder">
            <i class="fa-solid fa-user-shield card-icon"></i>
            <div class="card-label">Gestion rôles</div>
        </div>
    </a>

    <a href="{{ route('admin.statut-presences.index') }}" style="text-decoration: none;">
        <div class="card-placeholder">
            <i class="fa-regular fa-clock card-icon"></i>
            <div class="card-label">Gestion statut présence</div>
        </div>
    </a>

    <a href="{{ route('admin.statut-seances.index') }}" style="text-decoration: none;">
        <div class="card-placeholder">
            <i class="fa-regular fa-clipboard-check card-icon"></i>
            <div class="card-label">Gestion statut séance</div>
        </div>
    </a>

    <a href="{{ route('admin.semestres.index') }}" style="text-decoration: none;">
        <div class="card-placeholder">
            <i class="fa-solid fa-layer-group card-icon"></i>
            <div class="card-label">Gestion semestres</div>
        </div>
    </a>

    <a href="{{ route('admin.annees.index') }}" style="text-decoration: none;">
        <div class="card-placeholder">
            <i class="fa-solid fa-calendar-days card-icon"></i>
            <div class="card-label">Gestion années académiques</div>
        </div>
    </a>
</main>
@endsection
