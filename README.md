# Application Symfony 8.0

Application web construite avec Symfony 8.0, utilisant Bootstrap pour le style et SQLite comme base de données.

## Caractéristiques

- **Symfony 8.0** avec configuration webapp complète
- **Base de données SQLite** pour faciliter le développement
- **Bootstrap 5** via AssetMapper (pas de CSS personnalisé)
- **Authentification** avec formulaire de login
- **Rôles utilisateur** : USER et ADMIN
- **Tests automatisés** avec PHPUnit
- **Analyse statique** avec PHPStan et ECS
- **Respect des principes SOLID**

## Installation

1. Installer les dépendances :
```bash
composer install
```

2. Créer la base de données :
```bash
php bin/console doctrine:migrations:migrate
```

3. Démarrer le serveur :
```bash
symfony serve
```

L'application sera accessible à l'adresse : http://localhost:8000

## Création d'utilisateurs

Les utilisateurs sont créés via une commande console. Aucun formulaire d'inscription n'est disponible.

```bash
php bin/console app:create-user
```

La commande vous demandera :
- **Email** : L'adresse email de l'utilisateur
- **Rôle** : USER ou ADMIN

Un mot de passe sera automatiquement généré et affiché dans le terminal. **Conservez-le précieusement**.

Exemple de sortie :
```
Create a new user
==================

Email address: admin@example.com
Select role (default: USER): ADMIN

[OK] User created successfully!

 ------- ----------------------
  Field   Value
 ------- ----------------------
  Email   admin@example.com
  Role    ADMIN
  Generated Password   xY9#mK2$pL4@wN8z
 ------- ----------------------

[WARNING] Please save this password securely. It will not be shown again.
```

## Tests

Exécuter les tests :
```bash
php bin/phpunit
```

## Analyse de code

### PHPStan (analyse statique niveau 8)
```bash
vendor/bin/phpstan analyse
```

### ECS (vérification du style de code)
```bash
vendor/bin/ecs check
```

Pour corriger automatiquement :
```bash
vendor/bin/ecs check --fix
```

## Structure

- `src/Entity/User.php` : Entité utilisateur avec support des rôles
- `src/Controller/SecurityController.php` : Contrôleur d'authentification
- `src/Command/CreateUserCommand.php` : Commande de création d'utilisateurs
- `templates/security/login.html.twig` : Page de login (page d'accueil)
- `tests/` : Tests unitaires et fonctionnels

## Sécurité

- Les mots de passe sont hachés automatiquement avec l'algorithme auto de Symfony
- Protection CSRF activée sur tous les formulaires
- Session sécurisée avec hachage CRC32C des mots de passe
