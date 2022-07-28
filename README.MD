# PROJET ECOMMERCE SYMFONY 5

Disponible avec un jeux de fausse données livrer par Faker.Projet complet avec User et role admin, category et product. Mais aussi page de paiement avec l'api Stripe et dashboard admin

Créer en php 7.4.9 et mysql 5.7 avec Symfony 5.4, bootstrap 4

## Demo 

Visible : https://ecommerce-symfony-5.herokuapp.com

## Installation:
composer require
composer update
php bin/console doctrine:database:create 
php bin/console doctrine:migration:migrate (décommenter les migrations dans les methodes up())

php bin/console doctrine:fixtures:load --no-interaction

## Statut
En cours de développement

## Connexion

Admin:
admin@admin.fr -> admin

User:
user1@gmail.com -> password

## Paiement

modifier le .env et rentrer votre propre clé api secret et public stripe

Pour tester les paiements et les différents état possible ( accepter, refuser, en attente de double authentification)
voici le lien de la doc Stripe pour obtenir les numeros de carte bancaire adequat a vos tests : https://stripe.com/docs/testing?numbers-or-method-or-token=card-numbers


