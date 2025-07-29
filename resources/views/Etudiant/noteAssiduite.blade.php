@extends('layouts.etudiant')
@section('title','Note d’assiduité')

@section('content')
<div class="dashboard-container px-4 py-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">🎓 Assiduité par matière</h2>

    @if($assiduite->isEmpty())
        <p class="text-gray-600">Aucune donnée pour le moment.</p>
    @else
        <div class="space-y-4">
            @foreach($assiduite as $item)
                <div class="dashboard-card flex justify-between items-center bg-white shadow-md rounded-lg px-4 py-3 border border-gray-200">
                    <span class="text-gray-700 font-medium">{{ $item['matiere'] }}</span>
                    <span class="text-indigo-600 font-semibold">{{ $item['note'] }}/20</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
