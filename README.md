# [PHP] - P8 Openclassrooms - Todo & Co

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/8d419ad43d7f4a089a22d7dd81f265e1)](https://www.codacy.com/manual/thomas-claireau/PHP-P8-Openclassrooms?utm_source=github.com&utm_medium=referral&utm_content=thomas-claireau/PHP-P8-Openclassrooms&utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/bf44758efb762dc82604/maintainability)](https://codeclimate.com/github/thomas-claireau/PHP-P8-Openclassrooms/maintainability)

The project is hosted [online](http://todoandco.thomas-claireau.fr/).

## Setup project

```text
~ git clone https://github.com/thomas-claireau/PHP-P8-Openclassrooms.git
~ cd PHP-P8-Openclassrooms
```

The project runs on Symfony 4.2, so it is necessary that you have Composer installed on your machine.

To download Composer, [go here](https://getcomposer.org/download/).

Once downloaded, write this at the root of the project:

```text
~ composer install
```

If asked, choose "Yes for all packages" :

```text
Do you want to execute this recipe?
[y] Yes
[n] No
[a] Yes for all packages, only for the current installation session
[p] Yes permanently, never ask again for this project
```

Then install the front dependencies of the project.

For this, you must have NodeJs on your machine. To install it, [follow this link](https://nodejs.org/en/download/).

Write this at the root of the project:

```text
~ npm install && npm run build
```

### Notes

#### Access database

The project is delivered without a database. This means that you must add your configuration, in the `.env` and `.env.test` files, in the`DATABASE_URL` part.

Follow the following code:

```text
# .env

DATABASE_URL=mysql://'DB_USER':'DB_PASS'@DB_HOST/DB_NAME?serverVersion=5.7
```

```text
# .env.test - I advise you to use another database

DATABASE_URL=mysql://'DB_USER':'DB_PASS'@DB_HOST/DB_NAME?serverVersion=5.7
```

Don't forget to modify your passphrase, to secure your application:

```text
# .env

APP_SECRET=YOUR_PASSPHRASE
```

#### SQL injection and structure of the project

To obtain a structure similar to our project at the database level, recreate the DB by writing the following command, at the root of the project:

```text
~ php bin/console doctrine:schema:create
```

After creating your database, you can also inject a dataset by writing the following command:

```text
~ php bin/console doctrine:fixtures:load
```

### Run the project

At the root of the project, and in two different terminal:

-   To start the development server, run an `npm run dev-server`.
-   To launch the symfony server, run a `php bin / console server: run`.

### Authentication

In the project fixtures (`src/DataFixtures`), add your own account.

You can also use the following account, provided you have launched the fixtures:

-   username: admin
-   password: admin

More information is available on authentication [in the documentation](https://github.com/thomas-claireau/PHP-P8-Openclassrooms/wiki/Documentation).

### Run Tests

Run phpunit tests by following command :

```
~ php bin/console doctrine:fixtures:load && php bin/phpunit
```

## ✔️ Projet validé

Commentaire de l'évaluateur :

1. Évaluation globale du travail réalisé par l’étudiant (en spécifiant les critères non-validés si le projet est à retravailler) :

Projet validé.

2. Évaluation des livrables selon les critères du projet :

Les livrables correspondent à ce qui est demandé.
La nouvelle version de l’application est opérationnelle.

3. Évaluation de la présentation orale et sa conformité aux attentes :

Durée totale : 32:16 (dont 22:20 pour la présentation).
La présentation orale est claire et bien structurée.
Bonne maîtrise à la fois technique et des processus de développement.
Note : suite à un problème technique, Thomas a dû redémarrer son pc. La présentation s’est passée normalement et l’incident a eu lieu peu après le démarrage des questions. La soutenance a donc été interrompue entre 23:00 et 23:57 (le temps de se rendre compte de la coupure) et l’enregistrement a été mis en pause. Une fois le problème résolu, nous avons repris la soutenance à l’endroit où nous avions été interrompus.

4. Évaluation des nouvelles compétences acquises par l'étudiant :

Les compétences techniques requises pour la validation de la mission sont acquises.

5. Points positifs (au moins 1) :

Support de présentation clair.
Documentation technique claire et bien présentée.
Utilisation de Webpack pour améliorer les performances.
Documentation du processus de contribution au projet.
6. Axes d'amélioration (au moins 1) :

Bien revoir la différence entre tests unitaires et tests fonctionnels.
