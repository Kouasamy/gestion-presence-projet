@extends('layouts.admin')

@section('title', 'Ajouter une classe')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Ajouter une classe</h2>
            <a href="{{ route('admin.classes.index') }}">
                Liste des classes
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

        <form action="{{ route('admin.classes.store') }}" method="POST">
            @csrf
            <div>
                <label for="nom_classe" class="form-label">Nom de la classe</label>
                <input type="text"
                       name="nom_classe"
                       id="nom_classe"
                       class="form-input"
                       placeholder="Entrez le nom de la classe"
                       value="{{ old('nom_classe') }}"
                       required>
            </div>

            <button type="submit" class="form-button">
                Ajouter une classe
            </button>
        </form>
    </div>
</div>
@endsection
