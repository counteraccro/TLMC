# TLMC

Installation du projet
Le projet fonctionne avec les versions suivantes :

Version de PHP : 7.1
Mysql : 5.0.12
Apache : 2.4.33

Jquery 3.3.1
Bootstrap 4.1.2

Installation
------------

Étape 1 : cloner le dépôt GIT

`https://github.com/counteraccro/TLMC.git`

Étape 2 : Installer Symfony

`composer update`

Étape 3 : installer la base de données

`php bin/console doctrine:database:create`

Étape 4 : récupération des tables de la base de données

`php bin/console doctrine:schema:update --force`

Étape 5: installation des fixtures

`php bin/console doctrine:fixture:load`

Étape 6: accès au projet via l'url

`http://localhost:8000`


Dépendances
------------

doctrine/data-fixtures: ^1.3
sensio/framework-extra-bundle: ^5.2
symfony/asset: ^4.1
symfony/config: ^4.1
symfony/console: ^4.1
symfony/dependency-injection: ^4.1
symfony/expression-language: ^4.1
symfony/flex: ^1.0
symfony/form: ^4.1
symfony/framework-bundle: ^4.1
symfony/lts: ^4@dev
symfony/monolog-bundle: ^3.1
symfony/orm-pack: *
symfony/process: ^4.1
symfony/security-bundle: ^4.1
symfony/serializer: ^4.1
symfony/serializer-pack: *
symfony/swiftmailer-bundle: ^3.1
symfony/translation: ^4.1
symfony/twig-bundle: ^4.1
symfony/validator: ^4.1
symfony/web-link: ^4.1
symfony/webpack-encore-pack: *
symfony/yaml: ^4.1
twig/extensions: ^1.5