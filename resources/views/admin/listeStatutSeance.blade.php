<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Liste Statuts de Présence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">

    <a href="{{ route('statutpresence.create') }}" class="btn btn-primary mb-4">
        Ajouter un statut de présence
    </a>

    <h2>Liste des Statuts de Présence</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom du statut de présence</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($statuts as $statut)
                <tr>
                    <td>{{ $statut->id }}</td>
                    <td>{{ $statut->nom_statut_presence }}</td>
                    <td>
                        <a href="{{ route('statutpresence.edit', $statut->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('statutpresence.destroy', $statut->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Confirmer la suppression ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
