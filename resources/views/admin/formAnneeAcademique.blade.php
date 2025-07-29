@extends('layouts.admin')

@section('title', 'Ajouter une année académique')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Ajouter une année académique</h2>
            <a href="{{ route('admin.annees.index') }}">
                Liste des années
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

        <form action="{{ route('admin.annees.store') }}" method="POST">
            @csrf
            <div>
                <label for="annee" class="form-label">Année académique</label>
                <input type="text"
                       name="annee"
                       id="annee"
                       class="form-input"
                       placeholder="Ex: 2024-2025"
                       value="{{ old('annee') }}"
                       required>
            </div>

            <button type="submit" class="form-button">
                Ajouter une année
            </button>
        </form>
    </div>
</div>
@endsection
