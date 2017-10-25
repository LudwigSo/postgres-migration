# postgres-migration

Lightweight php migration tool for PostgreSQL databases.

## Getting Started


### Prerequisites

To use this tool you need to have installed:

```
Php 7.1+
PostgreSQL 9.6+
```
Details: [composer.json](composer.json) 

### Installing

```
composer require ludwig-br/postgres-migration
```

After downloading with composer or manually you need to do configurations. Every part you may need to touch is marked with a "ToDo"-statement.
- copy the content of the 'example' directory into your project

```
.../my_project/database/migrations/
```
- if you want to use your own config files implement the published Interfaces and delete the [config/Database.php](example/config/Database.php) and [config/Directories.php](example/config/Directories.php)
- otherwise alter the [config/Database.php](example/config/Database.php) and [config/Directories.php](example/config/Directories.php) and fill in your information
- alter the [config/includes.php](example/config/includes.php) with correct paths and class calls

After finishing the configuration you should get a security copy of you database.
```
https://www.postgresql.org/docs/current/static/backup.html
```
Finally we can generate our migration files!
```
cd .../my_project/database/migration
php generate.php
```
The files will be placed in the directories specified in [config/Directories.php](example/config/Directories.php) for restoration or updating the database after small changes use
```
cd .../my_project/database/migration
php reload.php
```


## Additional Information

This project is meant to offer an easy and lightweight way to integrate your (PostgreSQL) Database into your version control system. Furthermore minor changes to the database schema during developement can be applied easily.
During generation your database will be altered, therefore please get a security copy before you use this tool. I advice using this tool only in a development environment. 

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details