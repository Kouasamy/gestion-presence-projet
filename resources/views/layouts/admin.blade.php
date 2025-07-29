<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Administration')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <img src="https://www.ifran-ci.com/parent/img/logo-ifran-actualise.jpg" alt="Logo" class="logo-icon" />
            </div>

            @if(Auth::user()->role->nom_role === 'coordinateur')
            <nav class="nav-menu">
                <div class="nav-menu-container">
                    <ul>
                        <li class="{{ request()->routeIs('coordinateur.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.dashboard') }}" title="Tableau de bord">
                                <i class="fa-solid fa-house" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('coordinateur.seances.*') && !request()->has('filter') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.seances.index') }}" title="Liste des séances">
                                <i class="fa-solid fa-calendar-days" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('coordinateur.seances.presences.*') || (request()->routeIs('coordinateur.seances.*') && request('filter') === 'attendance') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.seances.index') }}?filter=attendance" title="Liste des présences">
                                <i class="fa-solid fa-clipboard-user" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('coordinateur.justification.*') || (request()->routeIs('coordinateur.seances.*') && request('filter') === 'justification') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.seances.index') }}?filter=justification" title="Liste des justifications">
                                <i class="fa-solid fa-file-circle-check" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('coordinateur.emploiDuTemps.*') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.seances.index') }}?view=timetable" title="Emploi du temps">
                                <i class="fa-solid fa-clock" style="font-size: 20px;"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        @else
            <nav class="nav-menu">
                <div class="nav-menu-container">
                    <ul>
                        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}" title="Tableau de bord">
                                <i class="fa-solid fa-house" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.user.*') || request()->routeIs('admin.user.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.user.index') }}" title="Gestion des utilisateurs">
                                <i class="fa-solid fa-users" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.cours.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.cours.index') }}" title="Gestion des cours">
                                <i class="fa-solid fa-book-open" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.classe.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.classes.index') }}" title="Gestion des classes">
                                <i class="fa-solid fa-school" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.role.index') }}" title="Gestion des rôles">
                                <i class="fa-solid fa-user-shield" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.statutpresence.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.statut-presences.index') }}" title="Gestion des statuts de présence">
                                <i class="fa-regular fa-clock" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.statut-seances.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.statut-seances.index') }}" title="Gestion des statuts de séance">
                                <i class="fa-regular fa-clipboard-check" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.semestres.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.semestres.index') }}" title="Gestion des semestres">
                                <i class="fa-solid fa-layer-group" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.annees.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.annees.index') }}" title="Gestion des années académiques">
                                <i class="fa-solid fa-calendar-days" style="font-size: 20px;"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        @endif

            <div class="header-icons">
                <button class="icon-btn">
                    <i class="fa-solid fa-bell" style="font-size: 20px; color: white;"></i>
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="icon-btn">
                        <i class="fa-solid fa-right-from-bracket" style="font-size: 20px; color: white;"></i>
                    </button>
                </form>
                <img class="user-avatar" src="https://randomuser.me/api/portraits/women/68.jpg" alt="User Avatar" />
            </div>
        </header>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Tous droits réservés | IFRAN
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
