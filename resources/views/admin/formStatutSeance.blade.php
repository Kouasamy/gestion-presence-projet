@extends('layouts.admin')

@section('title', 'Ajouter un statut de séance')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Ajouter un statut de séance</h2>
            <a href="{{ route('admin.statut-seances.index') }}">
                Liste des statuts
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        @if ($errors->any())
            <div class="form-error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="form-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.statut-seances.store') }}" method="POST">
            @csrf
            <div>
                <label for="nom_seance" class="form-label">Nom du statut de séance</label>
                <input type="text"
                       name="nom_seance"
                       id="nom_seance"
                       class="form-input"
                       placeholder="Entrez le nom du statut"
                       value="{{ old('nom_seance') }}"
                       required>
            </div>

            <button type="submit" class="form-button">
                Ajouter un statut
            </button>
        </form>
    </div>
</div>
@endsection
