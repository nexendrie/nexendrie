Installation instructions
=========================

Requirements
------------

Obviously, you need PHP. Version 7.4 or later is required. Then you need web server (Apache is a safe bet but nginx or even PHP built-in server should be fine) and sql server (MySql/MariaDb).
The game uses Composer to manage its dependencies, so you have to have it installed. You also need Git if you want to contribute.

Downloading
-----------

Clone the repository with git clone. Alternatively, you can download the source code from GitLab/GitHub in a archive.

Auto install
------------

After cloning the repository, you have to install the dependencies and create local configuration file and database with basic data. You can do that manually but a tool called [Phing](https://www.phing.info) can make it easier for you. Just download it and run *phing install -Denvironment=development*. After running it you can skip to part Web server.

Local configuration
-------------------

After that, you need to create file /app/config/local.neon with local settings for database and application. Use app/config/local.sample.neon as template.

Dependencies
------------

The game uses Composer to manage its dependencies. If you do not have them installed, run *composer install* to obtain them. Sometimes, required/used versions of dependencies change so update them locally on regular basics with the same command.

Database
--------

The game needs a database to store its data. We use nextras/orm with nextras/dbal to access it which currently supports only MySQL/MariaDB, PostgreSQL and MS SQL. We use Phinx for database migrations which in theory supports all these servers but some features work only on MySQL/MariaDB. Our config defines *production* and *testing* environments if their config (.neon) file exist.

Phinx is installed alongside other dependencies via Composer. Once you have it installed, do not forget to write access data (name of database, username and password) to file app/config/local.neon. Then you can run the migrations.

Web server
----------

### Apache

If you're using Apache, you have little work to do as the repository contains all needed .htaccess files. Just set up a simple virtual host, no special configuration is needed.

Example of virtual host configuration:

```apacheconfig
<VirtualHost nexendrie.localhost:80>
    ServerName nexendrie.localhost
    DocumentRoot "/var/www/html/nexendrie/www"
</VirtualHost>
```

(We strongly advise that the server name ends with .localhost, so it is considered a secure context by web browsers.)

The document root for that virtual host (or its parent directory if it is withing /var/www/html) needs to have these settings:

```apacheconf
<Directory /var/www/html/nexendrie/www>
    AllowOverride All
    Require all granted
</Directory>
```

Make sure that mod_headers and mod_rewrite are enabled (they may not be enabled by default depending on os).

### Nginx

With nginx, you just need to add a new server configuration:

```nginx
server {
    listen 80;
    index index.php;
    server_name nexendrie.localhost;
    root /var/www/html/nexendrie/www;

    location / {
        try_files $uri /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass localhost:9000;
        fastcgi_index index.php;
        rewrite_log on;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME /var/www/html/nexendrie/www/$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param REMOTE_ADDR 127.0.0.1;
        fastcgi_pass_header Authorization;
    }
}
```

. Then you only need a running instance of php-fpm.

### Caddy

With Caddy, the setup is like with nginx (simple server configuration + php-fpm) but the server configuration is even simpler:

```
nexendrie.localhost {
    root * /var/www/html/nexendrie/www
    php_fastcgi php-fpm:9000
    file_server
}
```

.

### PHP built-in server

If you do not want to bother with setting up and configuring a web server for development/testing, you can just use PHP built-in server. Just run this command (from the project's root directory):

```bash
php -S localhost:8080 -t www
```

### Other servers

If you have any other server, you (currently) have to do all server configuration by yourself as there are no experts on them in the development team. An important thing to have (configured) is something like mod_rewrite on Apache as we use "cool urls". If you have figured things out, please, tell us so we can update this section for other developers/testers who (consider to) use that server.
