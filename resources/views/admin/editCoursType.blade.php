<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifier Cours et Type de Cours</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        /* (styles inchangés de formCours pour l’harmonie visuelle) */
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background: white;
        }

        .logo {
            height: 50px;
        }

        .nav-center {
            background-color: #2f3357;
            border-radius: 30px;
            padding: 8px 15px;
            display: flex;
            gap: 18px;
            align-items: center;
        }

        .nav-center .icon {
            width: 24px;
            height: 24px;
            fill: white;
            cursor: pointer;
        }

        .nav-center .icon.highlighted {
            background-color: #d81e4e;
            border-radius: 50%;
            padding: 5px;
        }

        .right-icons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .right-icons .icon {
            width: 24px;
            height: 24px;
            fill: #2f3357;
            cursor: pointer;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 60px;
            padding: 40px 20px;
        }

        .form-card {
            background-color: #35385c;
            border-radius: 20px;
            padding: 40px 30px 50px 30px;
            width: 320px;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-card a.top-link {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 12px;
            color: #c1c9e6;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-card a.top-link:hover {
            text-decoration: underline;
        }

        .form-card input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            outline: none;
        }

        .form-card button {
            margin-top: 25px;
            background-color: #d81e4e;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 10px 25px;
            font-size: 13px;
            cursor: pointer;
            display: block;
            margin-left: auto;
            margin-right: auto;
            transition: background-color 0.3s ease;
        }

        .form-card button:hover {
            background-color: #b71c3f;
        }

        footer {
            font-size: 12px;
            color: #666;
            padding: 15px 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #eee;
            position: fixed;
            bottom: 0;
            left: 0;
            background: #fff;
        }
    </style>
</head>

<body>
    <header>
        <img src="https://i.ibb.co/3cQ7Q7Z/ifran-logo.png" alt="IFRAN Logo" class="logo" />
    </header>

    <main>
        @if (isset($matiere))
            <form class="form-card" action="{{ route('admin.cours.update', $matiere->id) }}" method="POST">
                @csrf
                @method('PUT')
                <a href="{{ route('admin.cours.liste') }}" class="top-link" title="Retour à la liste des cours">
                    Liste des cours
                    <svg viewBox="0 0 24 24">
                        <path d="M10 17l5-5-5-5v10z" />
                    </svg>
                </a>
                <input type="text" name="nom_matiere" value="{{ $matiere->nom_matiere }}" placeholder="Nom du cours"
                    required />
                <button type="submit">Modifier le cours</button>
            </form>
        @endif

        @if (isset($typeCours))
            <form class="form-card" action="{{ route('admin.typecours.update', $typeCours->id) }}" method="POST">
                @csrf
                @method('PUT')
                <a href="{{ route('admin.cours.liste') }}" class="top-link" title="Retour à la liste des types">
                    Liste des types
                    <svg viewBox="0 0 24 24">
                        <path d="M10 17l5-5-5-5v10z" />
                    </svg>
                </a>
                <input type="text" name="nom_type_cours" value="{{ $typeCours->nom_type_cours }}"
                    placeholder="Nom du type de cours" required />
                <button type="submit">Modifier le type</button>
            </form>
        @endif
    </main>

    <footer>
        © Copyright 2024 | Tous droits réservés | IFRAN
    </footer>
</body>

</html>
