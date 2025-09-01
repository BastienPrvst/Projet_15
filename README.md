# Ina Zaoui

Il s'agit d'un site d'affichage et de gestion de photos et d'albums de photos. Une partie administrateur permet aux utilisateurs de gerer leurs propres médias, et la propriétaire d'avoir la main mise sur absolument tout.

Prérequis :
  - PHP 8.3 +
  - Le site est basé sur la version 7.3 de Symfony en attendant la LTS (Long Term Support) 7.4
  - Composer
  - PostgreSQL

Installation du projet : 

- Cloner le repository
- Le projet tourne avec PostgreSQL, donc si vous ne l'avez pas => https://www.postgresql.org/download/
- Lancer `composer install --with-all-dependencies` pour initier et mettre à jour votre composer 
- Définir votre mot de passe Postgre dans l'url BDD du .env. Ex: DATABASE_URL="postgresql://postgres:monmotdepasse@127.0.0.1:5432/nom_de_la_bdd"
- Lancer :
    `php bin/console doctrine database create`
    `php bin/console doctrine migration migrate`
    `php bin/console doctrine fixtures load`
  pour créer votre BDD et la remplir avec les fixtures.
- Pour la partie test, je vous invite à relancer ces trois commandes avec à la fin de chacunes --env=test
- Si vous n'êtes pas sur la branche DEV mais sur MAIN => `git checkout dev`

Pour vous connecter sur le projet : 

email: `admin@mail.com` mdp: `password`

Concernant les tests, le Doctrine Test Bundle est mis en place, il utilise un système de rollback après chaque modification en BDD d'un test, permettant de ne pas se soucier d'annuler des suppression, de remettre un nom etc.

Le fichier contributing.md détaille les directives de contributions au projet afin de le maintenir stable et sain.
