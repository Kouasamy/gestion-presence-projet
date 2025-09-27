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
