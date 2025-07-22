<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - IFRAN</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            margin-bottom: 30px;
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
            display: flex;
            align-items: center;
            justify-content: center;
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

        .cards-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px 60px;
            justify-content: center;
            padding: 20px 0;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card-placeholder {
            width: 220px;
            height: 150px;
            background-color: #202149d1;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            position: relative;
            padding: 20px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: transform 0.3s ease;
        }

        .card-placeholder:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.4);
        }

        .card-placeholder .card-icon {
            font-size: 80px;
            color: #fff;
            position: static;
        }

        .card-label {
            font-weight: 700;
            font-size: 18px;
            color: #fff;
            text-align: center;
            user-select: none;
        }

        .footer {
            text-align: center;
            padding: 15px 0;
            font-size: 0.9rem;
            color: #666;
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f9fafb;
            border-top: 1px solid #ddd;
            user-select: none;
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
                    <li class="active">
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
                    <li>
                        <a href="{{ route('admin.roles.create') }}"><i class="fa-solid fa-user-shield"
                                style="font-size: 20px;"></i></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa-solid fa-book-open" style="font-size: 20px;"></i></a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="header-icons">
            <button class="icon-btn"><i class="fa-solid fa-bell" style="font-size: 20px; color: white;"></i></button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="icon-btn">
                    <i class="fa-solid fa-right-from-bracket" style="font-size: 20px; color: white;"></i>
                </button>
            </form>
            <img class="user-avatar" src="https://randomuser.me/api/portraits/women/68.jpg" alt="User Avatar" />
        </div>
    </header>

    <main class="cards-row">
        <a href="{{ route('admin.user.create') }}" style="text-decoration: none;">
            <div class="card-placeholder">
                <i class="fa-solid fa-user-graduate card-icon"></i>
                <div class="card-label">Gestion des étudiants</div>
            </div>
        </a>
        <div class="card-placeholder">
            <i class="fa-solid fa-user-tie card-icon"></i>
            <div class="card-label">Gestion coordinateurs</div>
        </div>
        <div class="card-placeholder">
            <i class="fa-solid fa-chalkboard-user card-icon"></i>
            <div class="card-label">Gestion professeurs</div>
        </div>
        <div class="card-placeholder">
            <i class="fa-solid fa-school card-icon"></i>
            <div class="card-label">Gestion classes</div>
        </div>
        <a href="{{ route('admin.roles.create') }}" style="text-decoration: none;">
            <div class="card-placeholder">
                <i class="fa-solid fa-user-shield card-icon"></i>
                <div class="card-label">Gestion rôles</div>
            </div>
        </a>
        <div class="card-placeholder">
            <i class="fa-solid fa-book-open card-icon"></i>
            <div class="card-label">Gestion cours</div>
        </div>
        <div class="card-placeholder">
            <i class="fa-regular fa-clock card-icon"></i>
            <div class="card-label">Gestion statut séance</div>
        </div>
        <div class="card-placeholder">
            <i class="fa-regular fa-clipboard-check card-icon"></i>
            <div class="card-label">Gestion statut présences</div>
        </div>
    </main>

    <footer class="footer">
        © Copyright 2024 | Tous droits réservés | IFRAN
    </footer>
</body>

</html>
