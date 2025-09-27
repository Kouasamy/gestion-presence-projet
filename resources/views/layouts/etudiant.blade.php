<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Étudiant')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />


    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css', 'resources/css/responsive.css'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu-overlay" class="mobile-menu-overlay"></div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu">
            <div class="mobile-menu-header">
                <img src="https://www.ifran-ci.com/parent/img/logo-ifran-actualise.jpg" alt="Logo" />
            </div>

            <ul>
                <!-- Dashboard -->
                <li class="{{ request()->routeIs('etudiant.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('etudiant.dashboard') }}" class="{{ request()->routeIs('etudiant.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>

                <!-- Emploi du temps -->
                <li class="{{ request()->routeIs('etudiant.emploi') ? 'active' : '' }}">
                    <a href="{{ route('etudiant.emploi') }}" class="{{ request()->routeIs('etudiant.emploi') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Emploi du temps</span>
                    </a>
                </li>

                <!-- Absences -->
                <li class="{{ request()->routeIs('etudiant.absences') ? 'active' : '' }}">
                    <a href="{{ route('etudiant.absences') }}" class="{{ request()->routeIs('etudiant.absences') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-clock"></i>
                        <span>Mes absences</span>
                    </a>
                </li>

                <!-- Assiduité -->
                <li class="{{ request()->routeIs('etudiant.assiduite') ? 'active' : '' }}">
                    <a href="{{ route('etudiant.assiduite') }}" class="{{ request()->routeIs('etudiant.assiduite') ? 'active' : '' }}">
                        <i class="fa-solid fa-star-half-stroke"></i>
                        <span>Assiduité</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Header -->
        <header class="header">
            <div class="logo">
                <img src="https://www.ifran-ci.com/parent/img/logo-ifran-actualise.jpg" alt="Logo" class="logo-icon" />
            </div>

            <!-- Burger Menu Button -->
            <button id="burger-menu-button" class="burger-menu-button" aria-expanded="false" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="nav-menu desktop-nav">
    <div class="nav-menu-container">
        <ul>
            <!-- Dashboard -->
            <li class="{{ request()->routeIs('etudiant.dashboard') ? 'active' : '' }}">
                <a href="{{ route('etudiant.dashboard') }}" title="Tableau de bord">
                    <i class="fa-solid fa-gauge" style="font-size: 20px;"></i>
                </a>
            </li>

            <!-- Emploi du temps -->
            <li class="{{ request()->routeIs('etudiant.emploi') ? 'active' : '' }}">
                <a href="{{ route('etudiant.emploi') }}" title="Emploi du temps">
                    <i class="fa-solid fa-calendar-days" style="font-size: 20px;"></i>
                </a>
            </li>

            <!-- Absences -->
            <li class="{{ request()->routeIs('etudiant.absences') ? 'active' : '' }}">
                <a href="{{ route('etudiant.absences') }}" title="Mes absences">
                    <i class="fa-solid fa-user-clock" style="font-size: 20px;"></i>
                </a>
            </li>

            <!-- Assiduité -->
            <li class="{{ request()->routeIs('etudiant.assiduite') ? 'active' : '' }}">
                <a href="{{ route('etudiant.assiduite') }}" title="Assiduité">
                    <i class="fa-solid fa-star-half-stroke" style="font-size: 20px;"></i>
                </a>
            </li>
        </ul>
    </div>
</nav>

            <div class="header-icons">
                <x-notification-dropdown :notifications="$droppedSubjects ?? collect([])" />

                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.edit') }}" title="Mon profil">
                        <img class="user-avatar" src="{{ Auth::user()->photo_url }}" alt="{{ Auth::user()->nom }}" />
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

    <!-- Menu Burger Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sélectionner les éléments du menu
            const burgerButton = document.getElementById('burger-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

            // Fonction pour basculer l'état du menu
            function toggleMenu() {
                const isOpen = mobileMenu.classList.contains('open');

                if (isOpen) {
                    // Fermer le menu
                    mobileMenu.classList.remove('open');
                    mobileMenuOverlay.classList.remove('active');
                    document.body.classList.remove('menu-open');
                    burgerButton.setAttribute('aria-expanded', 'false');
                } else {
                    // Ouvrir le menu
                    mobileMenu.classList.add('open');
                    mobileMenuOverlay.classList.add('active');
                    document.body.classList.add('menu-open');
                    burgerButton.setAttribute('aria-expanded', 'true');
                }
            }

            // Ajouter les écouteurs d'événements
            if (burgerButton) {
                burgerButton.addEventListener('click', toggleMenu);
            }

            if (mobileMenuOverlay) {
                mobileMenuOverlay.addEventListener('click', toggleMenu);
            }

            // Fermer le menu lorsqu'un lien est cliqué
            const mobileMenuLinks = mobileMenu ? mobileMenu.querySelectorAll('a') : [];
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', toggleMenu);
            });

            // Fermer le menu lorsque la fenêtre est redimensionnée à une taille plus grande
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768 && mobileMenu && mobileMenu.classList.contains('open')) {
                    toggleMenu();
                }
            });
        });
    </script>
</body>
</html>
