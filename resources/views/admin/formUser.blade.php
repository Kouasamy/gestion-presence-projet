<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Formulaire Étudiant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            background-color: #ffffff;
            font-family: Arial, sans-serif;
            height: 100%;
            margin: 0;
        }

        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh;
            padding: 20px;
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

        .form-container {
            background-color: #2f3354;
            border-radius: 25px;
            padding: 40px 50px;
            height: 700px;
            width: 646px;
            box-sizing: border-box;
            color: white;
            position: relative;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .student-list-link {
            position: absolute;
            top: 50px;
            right: 30px;
            font-size: 14px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .student-list-link {
            text-decoration: underline;
        }

        .student-list-link svg {
            width: 14px;
            height: 14px;
            fill: white;
        }

        form {
            margin-top: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 25px;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: none;
            margin: 10px 0;
            font-size: 14px;
            box-sizing: border-box;
        }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        input[type="file"]::placeholder,
        input[type="password"]::placeholder {
            color: #000000;
        }

        input[type="file"] {
            cursor: pointer;
            background-color: white;
            color: #000000;
        }

        button {
            background-color: #e91e40;
            color: white;
            border: none;
            width: 35%;
            border-radius: 12px;
            padding: 18px 0;
            margin-top: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #c21534;
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

        .error-box {
            display: flex;
            justify-content: center;
            color: red;
            margin: auto auto;
            background-color: #ffe6e6;
            border: 1px solid red;
            border-radius: 10px;
            width: 40%;
            padding: 10px 10px;
        }
    </style>
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
                    <li class="active">
                        <a href="#">
                            <i class="fa-solid fa-user-graduate" style="font-size: 20px;"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#"><i class="fa-solid fa-user-tie" style="font-size: 20px;"></i></a>
                    </li>
                    <li>
                        <a href=""><i class="fa-solid fa-chalkboard-user" style="font-size: 20px;"></i></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa-solid fa-school" style="font-size: 20px;"></i></a>
                    </li>
                    <li>
                        <a href="{{ route('admin.roles.index') }}"><i class="fa-solid fa-user-shield" style="font-size: 20px;"></i></a>
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

<body>
    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="error-box" style="background-color: #d4edda; border-color: #28a745; color: #155724;">
            {{ session('success') }}
        </div>
    @endif
    <div class="form-wrapper">
        <div class="form-container">
            <a href="#" class="student-list-link">
                listes des utilisateurs
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M10 17l5-5-5-5v10z" />
                </svg>
            </a>

            <form action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="nom" placeholder="Nom" required />
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Mot de passe" required />
                <input type="password" name="password_confirmation" placeholder="Confirmez le mot de passe" required>
                <select name="role_id"d="role_id" required>
                    <option value="">-- Choisir un rôle --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->nom_role }}</option>
                    @endforeach
                </select>
                <input type="file" name="photo_path" accept="image/*"
                    placeholder="Selectionner la photo" required />
                <button type="submit">Ajouter un utilisateur</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        © Copyright 2024 | Tous droits réservés | IFRAN
    </footer>
</body>

</html>
