# TLMC
Installation du projet
Le projet fonctionne avec les versions suivantes :

Version de PHP : 7.1
Mysql : 5.0.12
Apache : 2.4.33

Jquery 3.3.1
Bootstrap 4.1.2

Installation

Étape 1 : cloner le dépôt GIT

https://github.com/counteraccro/TLMC.git

Étape 2 : Installer Symfony

'composer update'

Étape 3 : installer la base de données

'php bin/console doctrine:database:create'

Étape 4 : récupération des tables de la base de données

'php bin/console doctrine:schema:update --force'

Étape 5: installation des fixtures

'php bin/console doctrine:fixture:load'

Étape 6: accès au projet via l'url

'http://localhost:8000'
