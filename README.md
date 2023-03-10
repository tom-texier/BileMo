# OpenClassrooms - Développeur d'applications PHP / Symfony
## Projet 7 - Créer un web service exposant une API

[![Symfony Badge](https://img.shields.io/badge/Symfony-5.4-000000?style=flat-square&logo=symfony&logoColor=white/)](https://symfony.com/)
[![Composer Badge](https://img.shields.io/badge/Composer-2.4-6c3e22?style=flat-square&logo=composer&logoColor=white/)](https://getcomposer.org/)
[![PHP Badge](https://img.shields.io/badge/PHP-7.4-7a86b8?style=flat-square&logo=php&logoColor=white/)](https://www.php.net/)
[![Postman Badge](https://img.shields.io/badge/Postman-10.10.6-FF6C37?style=flat-square&logo=Postman&logoColor=white/)](https://www.postman.com/)

## Prérequis d'installation

Pour initialiser le projet, vous devrez avoir installé sur votre machine (les versions utilisées pour le projet sont indiquées ci-dessus) :
- Composer
- PHP
- Un SGBD
- Postman

## Installation

1. Cloner le projet à l'emplacement de votre choix
```shell
git clone https://github.com/tom-texier/BileMo.git
```

2. Se déplacer à la racine du projet
```shell
cd BileMo
```

3. Installer les dépendances Composer
```shell
composer install
```

4. Créer un fichier `.env.local` à la racine du projet

5. Ajouter les informations de connexion à la base de données dans ce fichier en copiant le code ci-dessous et remplacer par les informations de connexion de votre environnement
```dotenv
DATABASE_URL=mysql://username:password@127.0.0.1:3306/bileMo
```

6. Ajouter la ligne suivante dans ce même fichier en complétant par une chaîne de caractères aléatoires (chiffres et lettres) :
```dotenv
JWT_PASSPHRASE=
```

Votre fichier `.env.local` doit maintenant ressembler à cela :
```dotenv
###> doctrine/doctrine-bundle ###
DATABASE_URL=mysql://username:password@127.0.0.1:3306/bileMo
###< doctrine/doctrine-bundle ###

###> lexik/jwt-authentication-bundle ###
JWT_PASSPHRASE=XXXXXXXXXXXXXXXXXXXXXXXXXXX
###< lexik/jwt-authentication-bundle ###
```

7. Générer une paire de clé SSL en lançant la commande suivante :
```shell
php bin/console lexik:jwt:generate-keypair
```
*Vérifier que le dossier `/config/jwt/` a bien été créé et qu'il contient les 2 clés SSL (publique et privée)*

8. Monter la base de données et générer les données initiales
```shell
composer prepare
```
ou
```shell
php bin/console doctrine:database:drop --if-exists -f
php bin/console doctrine:database:create
php bin/console doctrine:schema:update -f
php bin/console doctrine:fixtures:load -n
```

9. Importer la configuration des routes dans Postman. Le fichier se trouve dans `/resources/BileMo.postman_collection.json`.

![Import 1](./resources/images/postman_1.png)
![Import 2](./resources/images/postman_2.png)
![Import 3](./resources/images/postman_3.png)


10. Démarrer l'application
```shell
symfony server:start
```
ou
```shell
php -S localhost:8000 -t public
```
**<u>Attention :</u> si votre application ne se lance pas en HTTPS, veuillez modifier la variable {{domain}} dans la configuration de la collection sur Postman et remplacer `https://` par `http://`**

![Variable Domain](./resources/images/postman_variable_domain.png)

---

Pour utiliser l'API, vous devez tout d'abord vous connecter (via la route `/api/login`) en tant que **Client** en renseignant dans le corps de la requête les identifiants ci-dessous (ils doivent être pré-renseigné normalement):
- <u>**username :**</u> customer1@gmail.com
- <u>**password :**</u> password

Cette route vous retourne un token que vous devez ajouter dans l'entête de chaque requête. Pour simplifier, vous pouvez l'ajouter dans la configuration de la collection.

![Authorization Bearer Token](./resources/images/postman_token.png)

---

### Une fois votre serveur local lancé, la documentation de l'API est disponible via la route `/api/doc`.