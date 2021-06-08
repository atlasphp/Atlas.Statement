# Common Table Expressions

Every _Statement_ supports Common Table Expressions. To add one or more
CTE to a _Statement_, call the `with*()` methods:

```php
// WITH cte_1 AS (SELECT ...)
$insert->with('cte_1', "SELECT ...")

// WITH cte_2 (foo, bar, baz) AS (SELECT ...)
$update->withColumns('cte_2', ['foo', 'bar', 'baz'], "SELECT ...");
```

> **Note:**
>
> You can use any kind of _Statement_ as a CTE, not just a SELECT.

To enable or disable recursive CTEs, call `withRecursive()`:

```php
// enable
$select->withRecursive();

// disable
$select->withRecursive(false);
```

Further, you can pass a _Statement_ object instead of a string:

```php
$cteSelect = Select::new('sqlite');
$cteSelect->...;

$delete->with('cte_3', $cteSelect);
```

> **Note:**
>
> Any values bound to the CTE _Statement_ will be transferred to the main
> _Statement_.
