Installation instructions
=========================

Requirements
------------
Obviously, you need PHP. Version 7.0 or later is required. Then you need web server (preferably Apache or Nginx) and sql server (MySql, PostgreSql, MariaDb, etc.).
The game uses Composer to manage its dependecies so you have to have it installed. You also need Git if you want to contribute.

Downloading
-----------
Clone the repository with git clone.

Auto install
------------

After cloning the repository, you have to install the dependencies and create certain folders, local configuration file and database with basic data. You can do that by hand if you wish but there is a script which will do that for you.
The scripts is called install.sh (yes, it is only for Unix-like systems). After running it you can skip to part Database.

Creating folders
----------------
Before you can start working (developing/testing) with the game, you have to create these empty folders:

- /app/temp/cache
- /app/temp/sessions
- /app/log

. They are used to store generated data and they have to exist else you won't be able to run the application.

Local configuration
-------------------
After that, you need to create file /app/config/local.neon with local settings for database and application. Use app/config/local.sample.neon as template.

Dependencies
------------
The game uses Composer to manage its dependencies. If you do not have them installed, run *composer install* to obtain them. Then, you update them on regular basics with *composer update*.

Database
--------
The game needs a database to store its data. We use nextras/orm to access it. It supports only MySql/MariaDB and PostgreSql. Before you can run the game for first time, you have to create tables and fill the with at least basic data. For now, you have to do everything by yourself. See the entites to get an idea about tables definitions.

After that, do not forget to write access data (name of database, username and password) to file app/config/local.neon so the game will know where to look for data.

Web server
----------
### Apache
If you're using Apache, you have little work to do as the repository contains all needed .htaccess files. However with that configuration you would have to clone the repository to server's root. If you want to have it in different location, edit accordingly line

```
RewriteBase /
```

in /.htaccess and (optionally) set up a virtual host.
### NGINX
If you have NGINX, you (currenty) have to do all server configuration by yourself.