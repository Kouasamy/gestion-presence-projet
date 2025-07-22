<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Formulaire de rôle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* Reset and base */
        * {
            box-sizing: border-box;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1a202c;
        }

        .nav-menu ul {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-menu-container {
            background-color: #202149;
            border-radius: 30px;
            padding: 8px 24px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 400px;
        }

        .nav-menu ul li {
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            color: #ffffff;
            transition: background-color 0.3s ease;
            user-select: none;
        }
        .nav-menu ul li a {
            color: white;
            text-decoration: none;
        }

        .nav-menu ul li.active,
        .nav-menu ul li:hover {
            background-color: #E61845;
            color: #ffffff;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 1.5rem;
            color: #1a202c;
        }

        .logo-icon {
            width: 131px;
            height: 45px;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icon-btn {
            background: #f0f2f5;
            border: none;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-btn:hover {
            background-color: #e2e6ea;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icon-btn {
            background: #202149;
            border: none;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .icon-btn:hover {
            background-color: #E61845;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }

        .form-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 80vh;
            /* header + footer approx height */
        }

        form {
            background-color: #35385c;
            /* dark blue */
            border-radius: 20px;
            padding: 40px 30px 50px 30px;
            width: 320px;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* "listes des roles" link top right */
        .role-list-link {
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

        .role-list-link:hover {
            text-decoration: underline;
        }

        /* arrow icon */
        .role-list-link svg {
            width: 14px;
            height: 14px;
            fill: #3a7bd5;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: none;
            font-size: 14px;
            outline: none;
        }

        input[type="text"]::placeholder {
            color: #999;
        }

        button {
            margin-top: 25px;
            background-color: #d81e4e;
            /* red */
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

        button:hover {
            background-color: #b71c3f;
        }

        footer {
            font-size: 12px;
            color: #666;
            padding: 15px 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #eee;
            font-family: Arial, sans-serif;
            position: fixed;
            bottom: 0;
            left: 0;
            background: #fff;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo">
            <img src="https://www.ifran-ci.com/parent/img/logo-ifran-actualise.jpg" alt="Logo" class="logo-icon" />

        </div>
        <nav class="nav-menu">
            <div class="nav-menu-container">
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa-solid fa-house" style="font-size: 20px;"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.user.create') }}">
                            <i class="fa-solid fa-user-graduate" style="font-size: 20px;"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.user.create') }}"><i class="fa-solid fa-user-tie"
                                style="font-size: 20px;"></i></a>
                    </li>
                    <li>
                        <a href="{{ route('admin.user.create') }}"><i class="fa-solid fa-chalkboard-user"
                                style="font-size: 20px;"></i></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa-solid fa-school" style="font-size: 20px;"></i></a>
                    </li>
                    <li class="active">
                        <a href="{{ route('admin.roles.index') }}"><i class="fa-solid fa-user-shield"
                                style="font-size: 20px;"></i></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa-solid fa-book-open" style="font-size: 20px;"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="header-icons">
            <button class="icon-btn" aria-label="Notifications">
                <i class="fa-solid fa-bell" style="font-size: 20px; color: white;"></i>
            </button>
            <button class="icon-btn" aria-label="Deconnexion">
                <i class="fa-solid fa-right-from-bracket" style="font-size: 20px; color: white;"></i>
            </button>

            <img class="user-avatar" src="https://randomuser.me/api/portraits/women/68.jpg" alt="User Avatar" />
        </div>
    </header>
    <div class="form-wrapper">
        <form action="{{ route('admin.statutseance.store') }}" method="post" novalidate>
            @csrf

            <a href="{{ route('admin.statutseance.liste') }}" class="statutSeance-list-link" title="Listes des statut Séance">
                listes des Statut Séance
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M10 17l5-5-5-5v10z" />
                </svg>
            </a>


            <input type="text" name="nom_seance" placeholder="Nom du statut de la séance" required value="{{ old('nom_seance') }}" />

            @error('nom_seance')
                <p style="color: #f44336; font-size: 12px;">{{ $message }}</p>
            @enderror

            <button type="submit">Ajouter un statut seance </button>

            @if (session('success'))
                <p style="color: #4caf50; font-size: 14px; margin-top: 10px;">
                    {{ session('success') }}
                </p>
            @endif
        </form>
    </div>
    <footer>
        © Copyright 2024| Tous droits réservés| IFRAN
    </footer>
</body>

</html>
