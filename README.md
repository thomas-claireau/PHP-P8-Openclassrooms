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
~ npm install
```

### Notes

#### Access database

The project is delivered without a database. This means that you must add your configuration, in the `.env` file, in the`DATABASE_URL` part.

Follow the following code:

```text
# .env

DATABASE_URL=mysql://'DB_USER':'DB_PASS'@DB_HOST/DB_NAME?serverVersion=5.7
```

Don't forget to configure your test database (in phpunit.xml) :

```xml
 <!-- phpunit.xml or phpunit.dist.xml -->

<server name="DATABASE_URL" value="mysql://DB_USER:DB_PASS@DB_HOST/DB_NAME?serverVersion=5.7" />
```

Don't forget too to modify your passphrase, to secure your application:

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
