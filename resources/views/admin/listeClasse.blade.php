<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .btn-icon {
            background-color: #2f3357;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-icon:hover {
            background-color: #1d203c;
        }
    </style>
</head>
<body class="p-4">

    <!-- Bouton de retour -->
    <a href="{{ route('admin.classe.create') }}" class="btn-icon mb-4">
        <i class="fas fa-arrow-left"></i>
        Retour à l’ajout
    </a>

    <h2 class="mb-4">🏫 Liste des classes</h2>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom de la classe</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($classes as $classe)
                <tr>
                    <td>{{ $classe->id }}</td>
                    <td>{{ $classe->nom_classe }}</td>
                    <td>
                        <a href="{{ route('admin.classe.edit', $classe->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                        <form action="{{ route('admin.classe.destroy', $classe->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Confirmer la suppression de cette classe ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
