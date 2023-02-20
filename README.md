# OpenClassrooms - Développeur d'applications PHP / Symfony
## Projet 7 - Créer un web service exposant une API

[![Symfony Badge](https://img.shields.io/badge/Symfony-5.4-000000?style=flat-square&logo=symfony&logoColor=white/)](https://symfony.com/)
[![Composer Badge](https://img.shields.io/badge/Composer-2.4-6c3e22?style=flat-square&logo=composer&logoColor=white/)](https://getcomposer.org/)
[![PHP Badge](https://img.shields.io/badge/PHP-7.4-7a86b8?style=flat-square&logo=php&logoColor=white/)](https://www.php.net/)

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

5. Créer un fichier `.env.local` à la racine du projet

6. Ajouter les informations de connexion à la base de données dans ce fichier en copiant le code ci-dessous et remplacer par les informations de connexion de votre environnement
```dotenv
DATABASE_URL=mysql://username:password@127.0.0.1:3306/bileMo
```

7. Générer une paire de clé SSL en lançant la commande suivante :
```shell
php bin/console lexik:jwt:generate-keypair
```
*Vérifier que le dossier `/config/jwt/` a bien été créé et qu'il contient les 2 clés SSL (publique et privée)*
**<u>Attention :</u>  cette commande a également ajouté à la fin du fichier `.env` les informations de vos clés. Pour éviter toute fuite de données, couper/coller ces lignes à la fin du fichier `.env.local` créé précédemment.**

8. Monter la base de données et générer les données initiales
```shell
composer prepare
```

8. Importer la configuration des routes dans Postman. Le fichier se trouve dans `/resources/BileMo.postman_collection.json`.


10. Démarrer l'application
```shell
symfony server:start
```
ou
```shell
php -S localhost:8000 -t public
```
**<u>Attention :</u> si votre application ne se lance pas en HTTPS, veuillez modifier la variable {{domain}} dans la configuration de la collection sur Postman et remplacer `https` par `http`**


---

Ci-dessous les informations de connexion générées par le chargement des données initiales :

- <u>**username :**</u> customer1@gmail.com
- <u>**password :**</u> password
