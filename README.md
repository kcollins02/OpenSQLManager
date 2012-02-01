#OpenSQLManager

OpenSQLManager is an attempt to create an alternative to Navicat that is free and open. It is build with PHP-GTK, so I'm looking for a way to create normal binaries. 

### Included pre-configured version of php for windows
Because php-gtk is such a pain to compile on Windows, I've put together this package from the latest php-gtk windows package in `php-gtk2-win.7z`.

## PHP Requirements
* Version 5.2 - 5.3.*
* PHP-Cario PECL extension
* Curl
* OpenSSL
* JSON
* PDO
	* PDO drivers for the databases you wish to use

## Planned Features
* CRUD (Create, Read, Update, Delete) functionality
* Database table creation and backup 

The databases currently slated to be supported are:

* [PostgreSQL](http://www.postgresql.org)
* [MySQL](http://www.mysql.com/)
* [SQLite](http://sqlite.org/)

Plan to implement, not support:

* ODBC


## Won't Support
Closed source DBs, like Oracle, MSSQL, etc. 

