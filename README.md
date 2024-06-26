# E-Commerce Symfony

## Équipe (A4 DFL)
- Hassina AYACHI
- Joanne MASSILLON
- Mehdi AL SID CHEIKH

## Installation

### Composer

Après avoir clone le repo, il vous suffit de lancer la commande suivante :
```bash
composer install
```

### Ajouter la database

- Afin de pouvoir utiliser le projet, vous dever créer une base de données et la configurer dans le fichier .env.local
- Créer votre fichier .env.local à la racine du projet et ajouter la ligne suivante :
```bash
DATABASE_URL="mysql://[user]:[password]@127.0.0.1:3306/[database_name]?serverVersion=8.0.32&charset=utf8mb4"
```

### Migrations

- Pour créer les tables de la base de données, vous pouvez lancer la commande suivante :
```bash
php bin/console doctrine:migrations:migrate
```

### Lancer le serveur

- Pour lancer le serveur, vous pouvez lancer la commande suivante :
```bash
symfony server:start
```
ou
```bash
symfony serve
```

### Mode Dev

- Pour passer en mode prod, vous devez modifier la ligne suivante dans le fichier .env :
```bash
APP_ENV=prod
```
- A noter, les pages d'erreurs ne s'affichent qu'en mode prod

### Fixtures

- Pour ajouter des données dans la base de données, vous pouvez lancer la commande suivante :
```bash
php bin/console doctrine:fixtures:load
```
- Vous pouvez ajouter l'option `--append` pour ajouter les fixtures sans supprimer les données déjà existantes

### Comptes

- Il y a 3 rôles différents :
    - ROLE_USER
    - ROLE_ADMIN
    - ROLE_SUPER_ADMIN
- Tous les comptes créés par défaut ont le rôle ROLE_USER
- 2 comptes ont déjà été créés dans les fixtures avec les rôles ROLE_ADMIN et ROLE_SUPER_ADMIN
    - ROLE_ADMIN : 
      - Email : admin@admin.com
      - Mot de passe : adminadmin
    - ROLE_SUPER_ADMIN :
      - Email : superadmin@superadmin.com
      - Mot de passe : superadmin

### Langues
- Vous pouvez changer la langue du site. Pour ce faire, aller dans le fichier config/packages/translation.yaml et modifier la ligne suivante :
```bash
default_locale: fr
```