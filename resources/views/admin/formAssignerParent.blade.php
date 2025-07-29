@extends('layouts.admin')

@section('title', 'Assigner un parent à un étudiant')

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    Assigner un parent à {{ $etudiant->user->nom }}
                </h2>
                <form method="POST" action="{{ route('admin.etudiants.assignerParent', $etudiant->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="parent_id" class="block font-medium">Parent</label>
                        <select name="parent_id" id="parent_id" class="form-input w-full" required>
                            <option value="">Sélectionner un parent</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="custom-button">Valider</button>
                        <a href="{{ route('admin.user.index') }}" class="custom-button bg-gray-400">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
