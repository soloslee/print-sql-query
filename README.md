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

## License

Laravel Json Response is licensed under [MIT license](http://opensource.org/licenses/MIT).
