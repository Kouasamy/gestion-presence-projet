# Système de Gestion de Présence des Étudiants - IFRAN(INSTITUT FRANÇAIS DU NUMÉRIQUE )


Ce projet est une application web développée pour digitaliser le relevé des présences des étudiants de l'IFRAN. L'application permet de gérer les présences par matière et par séance de cours, avec différents rôles utilisateurs (administrateur, coordinateur pédagogique, enseignant, étudiant, parent).

## Fonctionnalités Implémentées

### Administration
- Gestion des utilisateurs (création, modification, suppression)
- Gestion des rôles (admin, coordinateur, enseignant, étudiant, parent)
- Gestion des classes
- Gestion des cours et types de cours
- Gestion des années académiques et semestres
- Gestion des statuts de séance et de présence
- Assignation des parents aux étudiants

### Coordinateur Pédagogique
- Gestion des séances de cours (création, modification, suppression)
- Gestion de l'emploi du temps par classe
- Saisie des présences pour les cours en e-learning et workshops
- Justification des absences des étudiants
- Assignation des étudiants aux classes
- Visualisation des statistiques détaillées (taux de présence par étudiant/classe, volume de cours)

### Enseignant
- Consultation de l'emploi du temps personnel
- Saisie des présences pour les séances de cours
- Visualisation des étudiants "droppés" (taux de présence < 30%)

### Étudiant
- Consultation de l'emploi du temps
- Visualisation des absences
- Consultation de la note d'assiduité par matière
- Notification en cas de "drop" d'une matière (taux de présence < 30%)

### Parent
- Consultation de l'emploi du temps des enfants
- Visualisation des absences des enfants
- Consultation des statistiques de présence

### Statistiques et Calculs
- Calcul automatique de la note d'assiduité par étudiant et par matière
- Calcul du taux de présence par étudiant, par matière et par classe
- Génération de graphiques pour visualiser les taux de présence
- Génération de graphiques pour visualiser le volume de cours dispensés

## Comment Tester le Projet

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- MySQL ou MariaDB
- Node.js et NPM

### Installation

1. Cloner le dépôt
```bash
git clone <url-du-depot>
cd gestion-presence-projet
```

2. Installer les dépendances PHP
```bash
composer install
```

3. Installer les dépendances JavaScript
```bash
npm install
```

4. Copier le fichier d'environnement et configurer la base de données
```bash
cp .env.example .env
```
Modifier le fichier .env avec vos informations de base de données:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_presence
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

5. Générer la clé d'application
```bash
php artisan key:generate
```

6. Exécuter les migrations et les seeders
```bash
php artisan migrate --seed
```

7. Compiler les assets
```bash
npm run dev
```

8. Démarrer le serveur
```bash
php artisan serve
```

L'application sera accessible à l'adresse http://localhost:8000

### Comptes de Test

Après avoir exécuté les seeders, les comptes suivants seront disponibles pour tester l'application:

- **Administrateur**:
  - Email: admin@gmail.com
  - Mot de passe: admin12345

- **Coordinateur**:
  - Email: kouakou@gmail.com
  - Mot de passe: kouakou12345

- **Enseignant**:
  - Email: bernard@gmail.com
  - Mot de passe: bernard12345

- **Étudiant**:
  - Email: kouatekra@gmail.com
  - Mot de passe: kouat12345

- **Parent**:
  - Email: kouassi@gmail.com
  - Mot de passe: kouassi12345

## Test des Différentes Interfaces

### Interface Administrateur

1. Connectez-vous avec le compte administrateur
2. Explorez le tableau de bord administrateur
3. Testez la gestion des utilisateurs:
   - Créez un nouvel utilisateur
   - Modifiez un utilisateur existant
   - Supprimez un utilisateur 
4. Testez la gestion des classes:
   - Créez une nouvelle classe
   - Modifiez une classe existante
   - Supprimez une classe 
5. Testez la gestion des cours:
   - Créez un nouveau cours
   - Modifiez un cours existant
   - Supprimez un cours 
6. Testez la gestion des années académiques et semestres:
   - Créez une nouvelle année académique
   - Modifiez une année académique
   - Supprimez une année académique
   - Créez un nouveau 
   - Modifiez un semestre
   - Supprimez un semestre
7. Testez l'assignation d'un parent à un étudiant

### Interface Coordinateur

1. Connectez-vous avec le compte coordinateur
2. Explorez le tableau de bord coordinateur
3. Testez la gestion de l'emploi du temps:
   - Créez un nouvel emploi du temps pour une classe
   - Modifiez un emploi du temps existant
4. Testez la gestion des séances:
   - Créez une nouvelle séance
   - Modifiez une séance existante
   - Reportez une séance
   - Annulez(Supprimez) une séance
5. Testez la saisie des présences:
   - Sélectionnez une séance
   - Saisissez les présences pour les étudiants
6. Testez la justification des absences:
   - Consultez la liste des absences
   - Justifiez une absence
7. Testez l'assignation d'un étudiant à une classe
8. Explorez les statistiques détaillées

### Interface Enseignant

1. Connectez-vous avec le compte enseignant
2. Explorez le tableau de bord enseignant
3. Consultez l'emploi du temps personnel
4. Testez la saisie des présences:
   - Sélectionnez une séance
   - Saisissez les présences pour les étudiants
5. Vérifiez les notifications d'étudiants "droppés"

### Interface Étudiant

1. Connectez-vous avec le compte étudiant
2. Explorez le tableau de bord étudiant
3. Consultez l'emploi du temps
4. Consultez la liste des absences
5. Consultez la note d'assiduité par matière
6. Vérifiez les notifications de "drop" de matière

### Interface Parent

1. Connectez-vous avec le compte parent
2. Explorez le tableau de bord parent
3. Consultez l'emploi du temps des enfants
4. Consultez la liste des absences des enfants
5. Consultez les statistiques de présence

## Design Responsive

L'application est entièrement responsive et s'adapte à tous les types d'appareils (ordinateurs de bureau, tablettes, smartphones). Les interfaces utilisateur ont été optimisées pour offrir une expérience utilisateur fluide quelle que soit la taille de l'écran.

### Caractéristiques du design responsive:

- **Menu Burger Responsive**: Sur les appareils mobiles et tablettes, la navigation principale se transforme en un menu burger fluide et accessible, offrant une expérience utilisateur optimale.
- **Mise en page adaptative**: Les éléments de l'interface s'ajustent automatiquement en fonction de la taille de l'écran.
- **Tableaux responsifs**: Les tableaux de données se transforment en affichage en liste sur les petits écrans pour une meilleure lisibilité.
- **Menus adaptés**: La navigation est optimisée pour les écrans tactiles sur les appareils mobiles.
- **Formulaires flexibles**: Les formulaires s'adaptent à la largeur de l'écran pour faciliter la saisie des données.
- **Boutons et contrôles tactiles**: Les éléments interactifs sont dimensionnés pour être facilement utilisables sur les écrans tactiles.
- **Animations fluides**: Les transitions et animations du menu burger et des éléments interactifs sont optimisées pour offrir une expérience utilisateur fluide.

## Architecture du Projet

Le projet suit une architecture MVC (Modèle-Vue-Contrôleur) avec une séparation claire des responsabilités:

### Modèles
Les modèles représentent les entités de la base de données et leurs relations:
- User: utilisateur du système
- Role: rôle de l'utilisateur (admin, coordinateur, enseignant, étudiant, parent)
- Etudiant, Enseignant, Parent, Coordinateur: profils spécifiques liés aux utilisateurs
- Classe: classe d'étudiants
- Matiere: matière enseignée
- Cours: cours dispensé
- TypeCours: type de cours (présentiel, e-learning, workshop)
- Seance: séance de cours
- StatutSeance: statut d'une séance (planifiée, annulée, reportée)
- Presence: présence d'un étudiant à une séance
- StatutPresence: statut de présence (présent, absent, retard)
- JustificationAbsence: justification d'une absence
- AnneeAcademique: année académique
- Semestre: semestre d'une année académique
- HistoriqueReport: historique des reports de séances

### Contrôleurs
Les contrôleurs gèrent les requêtes HTTP et coordonnent les interactions:
- AdminController: gestion des fonctionnalités administrateur
- CoordinateurController: gestion des fonctionnalités coordinateur
- EnseignantController: gestion des fonctionnalités enseignant
- EtudiantController: gestion des fonctionnalités étudiant
- ParentController: gestion des fonctionnalités parent

### Services
Les services encapsulent la logique métier:
- UserService: gestion des utilisateurs
- RoleService: gestion des rôles
- EtudiantService: gestion des étudiants
- EnseignantService: gestion des enseignants
- ParentService: gestion des parents
- SeanceService: gestion des séances
- PresenceService: gestion des présences
- StatistiqueService: calcul des statistiques
- ClasseService: gestion des classes
- CoursService: gestion des cours
- AnneeAcademiqueService: gestion des années académiques
- SemestreService: gestion des semestres

### Repositories
Les repositories gèrent l'accès aux données:
- EtudiantRepository
- PresenceRepository
- SeanceRepository
- RoleRepository
- CoursRepository

### Vues
Les vues sont organisées par rôle utilisateur:
- Admin: vues pour l'administrateur
- Coordinateur: vues pour le coordinateur
- Enseignant: vues pour l'enseignant
- Etudiant: vues pour l'étudiant
- Parent: vues pour le parent
