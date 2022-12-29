# moodle4

Web LMS repository

# About:

This repo consists of Moodle custom webservices (backend) which is needed for the PWA application/mobile app (android & ios) available below: https://github.com/siveshversion/moodle4PWA

# prerequisites for absolute beginners with Installation steps (pov - Windows)

1. Recommended to use php 7.4+; so get Xampp installed from the below link:

https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/7.4.33/xampp-windows-x64-7.4.33-0-VC15-installer.exe

2. Modify the config.inc.php file in the phpmyadmin directory:

Open this path C:\xampp\phpMyAdmin\config.inc.php and do the below changes:

$cfg['Servers'][$i]['password'] = 'your_Own_Password';
$cfg['Servers'][$i]['AllowNoPassword'] = false;

3. Configure a default root password for MySQL/MariaDB

From the cmd go to C:\xampp\mysql\bin\ and run the below commands:

mysql -u root

ALTER USER 'root'@'localhost' IDENTIFIED BY 'your_Own_Password';
flush privileges;
exit;

4. Login with your new changed password in mysql/bin using the below command:

mysql -u root -p

5. Now create a new database and import the Database dump straightaway

mysql -u root -p

CREATE DATABASE moodle4db;

exit;

# Import the Database dump

import **moodle4db.sql** db file into the DB using below comment.

mysql -u root -p moodle4db < **repositorypath\moodle4db.sql**


6. Enable the php_extension - intl from the php.ini

extension=intl

7. Place the project folder in htdocs & start the installation:

Delete **config.php** file from your project directory

After that run the web LMS

http://localhost/moodle4-web-lms/install.php

# Installation configs:

Data directory: \xampp\moodle4data
Type : native/mariadb
Database host: localhost
Database name : moodle4db (your created database name)
Database user: root (mysql username)
Database password: your_Own_Password (mysql password)
Database port : 3306

Rest of the configs keep the default values as it is.

8. Upgrade Moodle Database now (optional)