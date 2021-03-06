#Developer Notes

##Programming Style
Follow the CodeIgniter [Style Guide](https://github.com/timw4mail/CodeIgniter/blob/develop/user_guide_src/source/general/styleguide.rst) - and:

* Stick to PHP 5.2 features - unless I have a PHP 5.3 bundle with PHP-GTK for Windows attached
* Do not use spaces to align code
* Do not use `global`, `eval`
* Do not use the error suppressor `@`
* Add a docblock to every method
* Use [heredoc](http://us2.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc) string syntax for multi-line SQL statements to minimize PHP escape characters
* Use prepared statements whenever possible

## PHP-Gtk Resources
* [Reference](http://gtk.php.net/manual/en/reference.php)
* [Official Tutorials](http://gtk.php.net/manual/en/tutorials.php)
* [Community site](http://php-gtk.eu/) - Contains various tutorials
* [php-gtk2 Cookbook](http://www.kksou.com/php-gtk2/) - Various tutorials, requires registration

## Database reference material
### Firebird
* [Interbase 6 Lang Ref](http://fbclient.googlecode.com/files/LangRef.pdf) - SQL Syntax (pdf)
* [Firebird Lang Update Ref](http://www.firebirdsql.org/file/documentation/reference_manuals/reference_material/html/langrefupd25.html) - SQL Syntax Updates
* [Meta Data Queries](http://www.alberton.info/firebird_sql_meta_info.html)

### MySQL
* [MySQL Syntax](http://dev.mysql.com/doc/refman/5.1/en/sql-syntax.html)
* [Optimizing SQL Statements](http://dev.mysql.com/doc/refman/5.1/en/statement-optimization.html)

### PostgreSQL
* [PostgreSQL Syntax](http://www.postgresql.org/docs/9.0/interactive/sql.html)
* [Performance Tips](http://www.postgresql.org/docs/9.0/interactive/performance-tips.html)
* [Meta Data Queries](http://www.alberton.info/postgresql_meta_info.html)

### SQLite
* [SQL Syntax](http://www.sqlite.org/lang.html)
* [Pragma SQL Syntax](http://www.sqlite.org/pragma.html) - Internal / Performance Stuff