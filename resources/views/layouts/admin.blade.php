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

            @if(Auth::user()->role->nom_role === 'coordinateur')
            <ul>
                <li class="{{ request()->routeIs('coordinateur.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('coordinateur.dashboard') }}" class="{{ request()->routeIs('coordinateur.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('coordinateur.seances.*') && !request()->has('filter') ? 'active' : '' }}">
                    <a href="{{ route('coordinateur.seances.index') }}" class="{{ request()->routeIs('coordinateur.seances.*') && !request()->has('filter') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Liste des séances</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('coordinateur.presences.*') || (request()->routeIs('coordinateur.seances.*') && request('filter') === 'attendance') ? 'active' : '' }}">
                    <a href="{{ route('coordinateur.presences.index') }}" class="{{ request()->routeIs('coordinateur.presences.*') || (request()->routeIs('coordinateur.seances.*') && request('filter') === 'attendance') ? 'active' : '' }}">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span>Liste des présences</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('coordinateur.etudiants.*') ? 'active' : '' }}">
                    <a href="{{ route('coordinateur.etudiants.index') }}" class="{{ request()->routeIs('coordinateur.etudiants.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span>Liste des étudiants</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('coordinateur.absences.*') ? 'active' : '' }}">
                    <a href="{{ route('coordinateur.absences.index') }}" class="{{ request()->routeIs('coordinateur.absences.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-clock"></i>
                        <span>Absences</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('coordinateur.emploiDuTemps.*') ? 'active' : '' }}">
                    <a href="{{ route('coordinateur.emploiDuTemps.index') }}" class="{{ request()->routeIs('coordinateur.emploiDuTemps.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-week"></i>
                        <span>Emploi du temps</span>
                    </a>
                </li>
            </ul>
            @endif

            @if(Auth::user()->role->nom_role === 'admin')
            <ul>
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.user.index') }}" class="{{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        <span>Gestion des utilisateurs</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.matieres.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.matieres.index') }}" class="{{ request()->routeIs('admin.matieres.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Gestion des matières</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.types-cours.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.types-cours.index') }}" class="{{ request()->routeIs('admin.types-cours.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags"></i>
                        <span>Gestion des types de cours</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.classes.index') }}" class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-school"></i>
                        <span>Gestion des classes</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.role.index') }}" class="{{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-shield"></i>
                        <span>Gestion des rôles</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.statut-presences.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.statut-presences.index') }}" class="{{ request()->routeIs('admin.statut-presences.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-clock"></i>
                        <span>Gestion des statuts de présence</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.statut-seances.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.statut-seances.index') }}" class="{{ request()->routeIs('admin.statut-seances.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-clipboard-check"></i>
                        <span>Gestion des statuts de séance</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.semestres.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.semestres.index') }}" class="{{ request()->routeIs('admin.semestres.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Gestion des semestres</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.annees.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.annees.index') }}" class="{{ request()->routeIs('admin.annees.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Gestion des années académiques</span>
                    </a>
                </li>
            </ul>
            @endif
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

            @if(Auth::user()->role->nom_role === 'coordinateur')
            <nav class="nav-menu desktop-nav">
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
                        <li class="{{ request()->routeIs('coordinateur.presences.*') || (request()->routeIs('coordinateur.seances.*') && request('filter') === 'attendance') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.presences.index') }}" title="Liste des présences">
                                <i class="fa-solid fa-clipboard-user" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('coordinateur.absences.*') || (request()->routeIs('coordinateur.seances.*') && request('filter') === 'justification') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.absences.index') }}" title="Liste des justifications">
                                <i class="fa-solid fa-file-circle-check" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('coordinateur.emploiDuTemps.*') ? 'active' : '' }}">
                            <a href="{{ route('coordinateur.emploiDuTemps.index') }}" title="Emploi du temps">
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
                        <li class="{{ request()->routeIs('admin.matieres.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.matieres.index') }}" title="Gestion des matières">
                                <i class="fa-solid fa-book-open" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.types-cours.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.types-cours.index') }}" title="Gestion des types de cours">
                                <i class="fa-solid fa-tags" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.classes.index') }}" title="Gestion des classes">
                                <i class="fa-solid fa-school" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.role.index') }}" title="Gestion des rôles">
                                <i class="fa-solid fa-user-shield" style="font-size: 20px;"></i>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.statut-presences.*') ? 'active' : '' }}">
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
                <img class="user-avatar" src="{{ Auth::user()->photo_url }}" alt="{{ Auth::user()->nom }}" />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="icon-btn">
                        <i class="fa-solid fa-right-from-bracket" style="font-size: 20px; color: white;"></i>
                    </button>
                </form>
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
