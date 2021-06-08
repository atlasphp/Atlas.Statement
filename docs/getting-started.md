# Getting Started

This library provides query statement builders for MySQL, Postgres, SQLite, and
Microsoft SQL Server. The statements are independent of any particular database
connection, though they work best with [PDO](https://php.net/pdo), PDO wrappers
such as[Atlas.Pdo](http://atlasphp.io/dymaxion/pdo), or query performers such
as [Atlas.Query](http://atlasphp.io/dymaxion/query).

## Installation

This package is installable and autoloadable via [Composer](https://getcomposer.org/)
as [atlas/statement](https://packagist.org/packages/atlas/statement).

```sh
$ composer require atlas/statement ^1.0
```

## Instantiation

Instantiate the relevant _Statement_ object using its static `new()` method,
and pass the name of the database driver to use for identifier quoting, limit
clauses, etc:

```php
use Atlas\Statement\Select;
use Atlas\Statement\Insert;
use Atlas\Statement\Update;
use Atlas\Statement\Delete;

$select = Select::new('sqlite');
$insert = Insert::new('sqlite');
$udpate = Update::new('sqlite');
$delete = Delete::new('sqlite');
```

## Execution

Note that you will need to transfer the _Statement_ query string and bound
values to a database connection of your choice to actually execute the query.
Here is one example example of how to do so:

```php
use PDO;
use Atlas\Statement\Select;

$select = Select::new('sqlite');

// ...

$pdo = new PDO('sqlite::memory:');
$sth = $pdo->prepare($select->getQueryString());

foreach ($select->getBindValueObjects() as $name => $value) {
    $sth->bindValue($name, $value->getValue(), $value->getType());
}

$sth->execute();
```

> **Tip:**
>
> The [Atlas.Query](http://atlasphp.io/dymaxion/query) package extends this
> library to add query execution methods directly to _Statement_ objects, thus
> removing the need to transfer the query string and bound values to a database
> connection.
