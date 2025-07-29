<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Coordinateur')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

            <nav class="nav-menu">
                <div class="nav-menu-container">
                    <ul>
                        <!-- Dashboard -->
                        <li class="{{ request()->routeIs('coordinateur.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.dashboard') }}" title="Tableau de bord">
                                <i class="fa-solid fa-house" style="font-size: 20px;"></i>
                            </a>
                        </li>

                        <!-- Séances -->
                        <li class="{{ request()->routeIs('coordinateur.seances.*') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.seances.index') }}" title="Gestion des séances">
                                <i class="fa-solid fa-calendar-days" style="font-size: 20px;"></i>
                            </a>
                        </li>


                        <!-- Absences -->
                        <li class="{{ request()->routeIs('coordinateur.absences.*') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.absences.index') }}" title="Gestion des absences">
                                <i class="fa-solid fa-user-xmark" style="font-size: 20px;"></i>
                            </a>
                        </li>

                        <!-- Emploi du temps -->
                        <li class="{{ request()->routeIs('coordinateur.emploiDuTemps.*') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.emploiDuTemps.index') }}" title="Emploi du temps">
                                <i class="fa-solid fa-clock" style="font-size: 20px;"></i>
                            </a>
                        </li>

                        <!-- Statistiques -->
                        <li class="{{ request()->routeIs('coordinateur.statistiques') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.statistiques') }}" title="Statistiques">
                                <i class="fa-solid fa-chart-line" style="font-size: 20px;"></i>
                            </a>
                        </li>

                    </ul>
                </div>
            </nav>

            <div class="header-icons">
                <x-notification-dropdown :notifications="$stats['etudiantsDroppes'] ?? collect([])" />

                <!-- Profil et Déconnexion -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.edit') }}" class="icon-btn" title="Mon profil">
                        <i class="fa-solid fa-user" style="font-size: 20px; color: white;"></i>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="icon-btn" title="Déconnexion">
                            <i class="fa-solid fa-right-from-bracket" style="font-size: 20px; color: white;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

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
