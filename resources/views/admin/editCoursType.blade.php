@extends('layouts.admin')

@section('title', isset($matiere) ? 'Modifier un cours' : 'Modifier un type de cours')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-center gap-6">
        @if (isset($matiere))
                <div class="w-full max-w-md">
                    <div class="form-container">
                        <a href="{{ route('admin.cours.index') }}" class="student-list-link">
                    Liste des cours
                    <svg viewBox="0 0 24 24">
                        <path d="M10 17l5-5-5-5v10z" />
                    </svg>
                </a>

                        @if ($errors->any())
                            <div class="error-box">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.cours.update', $matiere->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="nom_matiere" class="block text-sm font-medium text-white mb-2">
                                    Nom du cours
                                </label>
                                <input type="text" 
                                       name="nom_matiere" 
                                       id="nom_matiere"
                                       value="{{ $matiere->nom_matiere }}" 
                                       placeholder="Entrez le nom du cours" 
                                       required 
                                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />
                            </div>

                            <div class="flex justify-center">
                                <button type="submit" class="custom-button">
                                    <i class="fas fa-save mr-2"></i>
                                    Modifier le cours
                                </button>
                            </div>
            </form>
                    </div>
                </div>
        @endif

        @if (isset($typeCours))
                <div class="w-full max-w-md">
                    <div class="form-container">
                        <a href="{{ route('admin.cours.index') }}" class="student-list-link">
                            Liste des types de cours
                    <svg viewBox="0 0 24 24">
                        <path d="M10 17l5-5-5-5v10z" />
                    </svg>
                </a>

                        @if ($errors->any())
                            <div class="error-box">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.typecours.update', $typeCours->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="nom_type_cours" class="block text-sm font-medium text-white mb-2">
                                    Type de cours
                                </label>
                                <input type="text" 
                                       name="nom_type_cours" 
                                       id="nom_type_cours"
                                       value="{{ $typeCours->nom_type_cours }}" 
                                       placeholder="Entrez le type de cours" 
                                       required 
                                       class="w-full px-4 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500" />
                            </div>

                            <div class="flex justify-center">
                                <button type="submit" class="custom-button">
                                    <i class="fas fa-save mr-2"></i>
                                    Modifier le type
                                </button>
                            </div>
            </form>
                    </div>
                </div>
        @endif
        </div>
    </div>
</div>
@endsection
