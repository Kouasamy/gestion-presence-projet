@extends('layouts.admin')

@section('title', 'Ajouter un rôle')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Ajouter un rôle</h2>
            <a href="{{ route('admin.role.index') }}">
                Liste des rôles
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

        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div>
                <label for="nom_role" class="form-label">Nom du rôle</label>
                <input type="text"
                       name="nom_role"
                       id="nom_role"
                       class="form-input"
                       placeholder="Entrez le nom du rôle"
                       value="{{ old('nom_role') }}"
                       required>
            </div>

            <button type="submit" class="form-button">
                Ajouter un rôle
            </button>
        </form>
    </div>
</div>
@endsection
