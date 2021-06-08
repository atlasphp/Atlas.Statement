# Getting Started

## Installation

This package is installable and autoloadable via [Composer](https://getcomposer.org/)
as [atlas/statement](https://packagist.org/packages/atlas/statement).

```sh
$ composer require atlas/statement ^2.0
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
values to a database connection of your choice to actually be able to perform
the query. Use the _Statement_ methods `getQueryString()` and `getBindValues()`
for that purpose.

The _Prepare_ class is available an example of how to do so:

```php
use PDO;
use Atlas\Statement\Select;

$select = Select::new('sqlite');
// ...

$pdo = new PDO('sqlite::memory:');
$pdoStatement = Prepare::pdoStatement($pdo, $select);
$pdoStatement->execute();
```