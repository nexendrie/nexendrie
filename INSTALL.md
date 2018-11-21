Installation instructions
=========================

Requirements
------------

Obviously, you need PHP. Version 7.2 or later is required. Then you need web server (Apache is a safe bet) and sql server (preferably MySql, MariaDb or PostgreSQL but MS SQL might be also used).
The game uses Composer to manage its dependencies so you have to have it installed. You also need Git if you want to contribute.

Downloading
-----------

Clone the repository with git clone. Alternatively, you can download the source code from GitLab/GitHub in a archive.

Auto install
------------

After cloning the repository, you have to install the dependencies and create certain folders, local configuration file and database with basic data. You can do that manually but a tool called [Phing](https://www.phing.info) can make it easier for you. Just download it and run *phing install -Denvironment=development*. After running it you can skip to part Web server.

Creating folders
----------------

Before you can start working (developing/testing) with the game, you have to create these empty folders:

- /temp/cache
- /temp/sessions

. They are used to store generated data and they have to exist else you won't be able to run the application.

Local configuration
-------------------

After that, you need to create file /app/config/local.neon with local settings for database and application. Use app/config/local.sample.neon as template.

Dependencies
------------

The game uses Composer to manage its dependencies. If you do not have them installed, run *composer install* to obtain them. Sometimes, required/used versions of dependencies change so update them locally on regular basics with the same command.

Database
--------

The game needs a database to store its data. We use nextras/orm with nextras/dbal to access it which currently supports only MySQL/MariaDB, PostgreSQL and MS SQL. Before you can run the game for first time, you have to create tables and fill them with at least basic data (test data are not strictly necessary if you do not run tests on that database). Folder app/sqls contains all needed queries for both MySQL/MariaDB and PostreSQL. If you use MS SQL, tweak them accordingly before running.

After that, do not forget to write access data (name of database, username and password) to file app/config/local.neon so the game will know where to look for data.

Web server
----------

### Apache
If you're using Apache, you have little work to do as the repository contains all needed .htaccess files. Just set up a virtual host. No special configuration is needed.

### Other servers
If you have any other server, you (currently) have to do all server configuration by yourself as there are no experts on them in the development team. An important thing to have (configured) is something like mod_rewrite on Apache as we use "cool urls". If you have figured things out, please, tell us so we can update this section for other developers/testers who (consider to) use that server.
