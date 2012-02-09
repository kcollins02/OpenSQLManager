#OpenSQLManager

OpenSQLManager is an attempt to create an alternative to Navicat that is free and open. It is build with PHP-GTK, so I'm looking for a way to create normal binaries. 

### Pre-configured version of php for windows
Because php-gtk is such a pain to compile on Windows, I've put together this package from the latest php-gtk windows package. It's available in the downloads section.

## PHP Requirements
* Version 5.2 - 5.3.*
* PHP-Cairo PECL extension
* [PHP-GTK](http://gtk.php.net) PHP Extension
* Curl
* OpenSSL
* JSON
* PDO
	* PDO drivers for the databases you wish to use

## Want to Contribute?
See [Dev Guide](https://github.com/aviat4ion/OpenSQLManager/blob/master/DEV_README.md)

## Planned Features
* CRUD (Create, Read, Update, Delete) functionality
* Database table creation and backup 

The databases currently slated to be supported are:

* [Firebird](http://www.firebirdsql.org/)
* [MySQL](http://www.mysql.com/) / [MariaDB](http://mariadb.org/)
* [PostgreSQL](http://www.postgresql.org)
* [SQLite](http://sqlite.org/)


Plan to implement, not support:

* ODBC


## Won't Support
Closed source DBs, like Oracle, MSSQL, etc. 