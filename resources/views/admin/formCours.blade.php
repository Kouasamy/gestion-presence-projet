@extends('layouts.admin')

@section('title', 'Gestion des cours')

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2 class="text-white text-xl">Ajouter un cours</h2>
            <a href="{{ route('admin.cours.index') }}">
                Liste des cours
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Formulaire pour ajouter un cours -->
            <div>
                <h3 class="text-white text-lg mb-4">Nouveau cours</h3>
                <form action="{{ route('admin.cours.store') }}" method="POST">
                    @csrf
                    <div>
                        <label for="nom_matiere" class="form-label">Nom du cours</label>
                        <input type="text"
                               name="nom_matiere"
                               id="nom_matiere"
                               class="form-input"
                               placeholder="Entrez le nom du cours"
                               value="{{ old('nom_matiere') }}"
                               required>
                    </div>

                    <button type="submit" class="form-button">
                        Ajouter le cours
                    </button>
                </form>
            </div>

            <!-- Formulaire pour ajouter un type de cours -->
            <div>
                <h3 class="text-white text-lg mb-4">Nouveau type de cours</h3>
                <form action="{{ route('admin.cours.types.store') }}" method="POST">
                    @csrf
                    <div>
                        <label for="nom_type_cours" class="form-label">Nom du type de cours</label>
                        <input type="text"
                               name="nom_type_cours"
                               id="nom_type_cours"
                               class="form-input"
                               placeholder="Entrez le type de cours"
                               value="{{ old('nom_type_cours') }}"
                               required>
                    </div>

                    <button type="submit" class="form-button">
                        Ajouter le type
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
