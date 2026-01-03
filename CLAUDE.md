# Consignes et Guidelines du Projet

## Architecture et Principes

### Principes SOLID
- **S**ingle Responsibility Principle : Chaque classe a une seule responsabilité
- **O**pen/Closed Principle : Ouvert à l'extension, fermé à la modification
- **L**iskov Substitution Principle : Les classes dérivées doivent être substituables à leurs classes de base
- **I**nterface Segregation Principle : Interfaces spécifiques plutôt qu'une interface générale
- **D**ependency Inversion Principle : Dépendre des abstractions, pas des implémentations concrètes

### Testabilité
- L'application doit être **complètement testable**
- Utiliser l'injection de dépendances pour faciliter les tests
- Écrire des tests pour toutes les fonctionnalités importantes
- Tous les tests doivent passer avant tout commit

## Stack Technique

### Framework et Versions
- **Symfony 8.0** avec configuration webapp
- **PHP 8.5+**

### Base de Données
- **SQLite** uniquement
- Utiliser Doctrine ORM pour toutes les interactions avec la base de données
- Créer des migrations pour tous les changements de schéma

### Frontend et Assets
- **AssetMapper** pour la gestion des assets (pas de Webpack Encore)
- **Bootstrap 5** uniquement pour le style
- **INTERDICTION ABSOLUE** :
  - ❌ Pas de CSS personnalisé (ni dans des fichiers, ni inline)
  - ❌ Pas de Stimulus
  - ❌ Pas de Turbo
  - ❌ Pas de frameworks JS (React, Vue, etc.)
- Utiliser uniquement les classes utilitaires Bootstrap

### Outils de Qualité de Code

#### PHPStan (Analyse Statique)
- **Niveau 8** (le plus strict)
- Lancer avant chaque commit : `vendor/bin/phpstan analyse`
- Aucune erreur tolérée

#### ECS (Easy Coding Standard)
- Standards PSR-12, Common, Symplify, Strict
- Vérification : `vendor/bin/ecs check`
- Correction automatique : `vendor/bin/ecs check --fix`

#### PHPUnit
- Tous les tests doivent passer
- Exécution : `php bin/phpunit`
- Écrire des tests pour :
  - Les commandes
  - Les contrôleurs
  - Les entités avec logique métier
  - Les services

## Authentification et Utilisateurs

### Règles de Sécurité
- **Pas de formulaire d'inscription** : Les utilisateurs sont créés uniquement via commande
- Utilisation du système de sécurité Symfony natif
- Protection CSRF activée sur tous les formulaires
- Hash automatique des mots de passe

### Rôles Disponibles
- `ROLE_USER` : Utilisateur standard
- `ROLE_ADMIN` : Administrateur

### Création d'Utilisateurs
- Commande : `php bin/console app:create-user`
- La commande demande :
  - Email (validé)
  - Rôle (choix entre USER et ADMIN)
- Génération automatique d'un mot de passe sécurisé (16 caractères)

## Conventions de Code

### Structure des Fichiers
```
src/
├── Command/          # Commandes console
├── Controller/       # Contrôleurs HTTP
├── Entity/           # Entités Doctrine
├── Repository/       # Repositories Doctrine
└── Service/          # Services métier (si nécessaire)

tests/
├── Command/          # Tests des commandes
├── Controller/       # Tests des contrôleurs
└── ...               # Miroir de la structure src/
```

### Conventions de Nommage
- Classes : PascalCase
- Méthodes : camelCase
- Propriétés : camelCase
- Constantes : UPPER_SNAKE_CASE
- Namespaces : suivre la structure PSR-4

### Injection de Dépendances
- Toujours utiliser le constructor injection
- Utiliser des propriétés `readonly` quand possible
- Déclarer les types de manière stricte (`declare(strict_types=1)`)

### Documentation
- Les méthodes publiques complexes doivent avoir des docblocks
- Les commentaires doivent expliquer le "pourquoi", pas le "quoi"
- Éviter les commentaires évidents

## Workflow de Développement

### Avant de Commiter
1. Lancer les tests : `php bin/phpunit`
2. Lancer PHPStan : `vendor/bin/phpstan analyse`
3. Lancer ECS : `vendor/bin/ecs check --fix`
4. Vérifier que tout est vert ✅

### Ajout de Nouvelles Fonctionnalités
1. Écrire les tests d'abord (TDD recommandé)
2. Implémenter la fonctionnalité
3. Vérifier que les tests passent
4. Vérifier la qualité du code (PHPStan, ECS)
5. Créer une migration si nécessaire

### Modifications de la Base de Données
1. Modifier l'entité
2. Créer la migration : `php bin/console make:migration`
3. Vérifier la migration générée
4. Exécuter : `php bin/console doctrine:migrations:migrate`

## Interdictions Strictes

### CSS et Styles
- ❌ JAMAIS de fichiers CSS personnalisés
- ❌ JAMAIS de styles inline dans les templates
- ❌ JAMAIS de SASS/SCSS
- ✅ Utiliser uniquement les classes Bootstrap

### JavaScript
- ❌ JAMAIS de Stimulus
- ❌ JAMAIS de Turbo
- ❌ JAMAIS de frameworks frontend (React, Vue, Angular, etc.)
- ✅ JavaScript vanilla si absolument nécessaire
- ✅ Bootstrap JavaScript (inclus via AssetMapper)

### Sécurité
- ❌ JAMAIS de mots de passe en clair
- ❌ JAMAIS de requêtes SQL directes (utiliser Doctrine)
- ❌ JAMAIS de données utilisateur non validées
- ✅ Toujours valider et sanitiser les inputs
- ✅ Toujours utiliser les requêtes préparées (via Doctrine)

## Templates Twig

### Structure
- Tous les templates étendent `base.html.twig`
- Utiliser les blocks Symfony standard : `title`, `body`, `stylesheets`, `javascripts`

### Classes Bootstrap à Privilégier
- Layout : `.container`, `.row`, `.col-*`
- Composants : `.card`, `.btn`, `.form-control`, `.alert`, `.badge`
- Utilitaires : `.mt-*`, `.mb-*`, `.p-*`, `.text-*`, `.d-*`, `.justify-content-*`

### Exemple de Template Conforme
```twig
{% extends 'base.html.twig' %}

{% block title %}Mon Titre{% endblock %}

{% block body %}
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mt-5">
                <div class="card-body">
                    <h1 class="card-title">Titre</h1>
                    <p class="card-text">Contenu</p>
                    <button class="btn btn-primary">Action</button>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}
```

## Commandes Utiles

```bash
# Développement
symfony serve                              # Lancer le serveur

# Base de données
php bin/console make:migration             # Créer une migration
php bin/console doctrine:migrations:migrate # Exécuter les migrations

# Utilisateurs
php bin/console app:create-user            # Créer un utilisateur

# Tests et Qualité
php bin/phpunit                            # Tests
vendor/bin/phpstan analyse                 # Analyse statique
vendor/bin/ecs check                       # Vérifier le code style
vendor/bin/ecs check --fix                 # Corriger le code style

# Assets
php bin/console importmap:require <package> # Ajouter un package
php bin/console importmap:remove <package>  # Retirer un package
```

## En Cas de Doute

1. **Respecter les principes SOLID**
2. **Rester simple** : Ne pas sur-ingénier
3. **Utiliser Symfony** : Suivre les conventions Symfony
4. **Bootstrap uniquement** : Pas de CSS custom
5. **Tester** : Écrire des tests pour valider le comportement
6. **Qualité** : PHPStan niveau 8 et ECS doivent passer
