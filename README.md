Print SQL Query Middleware
=====

Installation
------------

Install using composer:

```bash
composer require soloslee/print-sql-query
```

Laravel
------------------

Add the middleware in `app/Http/Kernel.php`:

```php
\Soloslee\PrintSqlQuery\PrintSqlQuery::class,
```

And add the value `PRINT_SQL_QUERY=true` to `.env`:

## License

Laravel Print SQL Query is licensed under [MIT license](http://opensource.org/licenses/MIT).
