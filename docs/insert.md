# INSERT

## Building The Statement

### Into

Use the `into()` method to specify the table to insert into.

```php
$insert->into('foo');
```

### Columns

You can set a named placeholder and its corresponding bound value using the
`column()` method.

```php
// INSERT INTO foo (bar) VALUES (:bar)
$insert->column('bar', $bar_value);
```

Note that the PDO parameter type will automatically be set for strings,
integers, floats, and nulls. If you want to set a PDO parameter type yourself,
pass it as an optional third parameter.

```php
// INSERT INTO foo (bar) VALUES (:bar);
$insert->column('bar', $bar_value, \PDO::PARAM_LOB);
```

You can set several placeholders and their corresponding values all at once by
using the `columns()` method:

```php
// INSERT INTO foo (bar) VALUES (:bar)
$insert->columns([
    'bar' => $bar_value,
    'baz' => $baz_value
]);
```

However, you will not be able to specify a particular PDO parameter type when
doing do.

Bound values are automatically quoted and escaped; in some cases, this will be
inappropriate, so you can use the `set()` method to set column to an unquoted
and unescaped expression.

```php
// INSERT INTO foo (bar) VALUES (NOW())
$insert->set('bar', 'NOW()');
```

### RETURNING

Some databases (notably PostgreSQL) recognize a `RETURNING` clause. You can add
one to the _Insert_ using the `returning()` method, specifying columns as
variadic arguments.

```php
// INSERT ... RETURNING foo, bar, baz
$insert
    ->returning('foo')
    ->returning('bar', 'baz');
```

### Flags

You can set flags recognized by your database server using the `setFlag()`
method. For example, you can set a MySQL `LOW_PRIORITY` flag like so:

```php
// INSERT LOW_PRIORITY INTO foo (bar) VALUES (:bar)
$insert
    ->into('foo')
    ->column('bar', $bar_value)
    ->setFlag('LOW_PRIORITY');
```
