# Value Binding

You can bind values to a _Statement_ in various ways.

## Implicit Inline Binding

Many _Statement_ methods allow for inline binding of values. This means that the
provided value will be represented by an auto-generated placeholder name in the
query string, and the value itself will be retained for binding into that
placeholder at query execution time.

For example, given this statement ...

```php
$select
    ->columns('*')
    ->from('foo')
    ->where('bar = ', $bar_value); // binds $bar_value inline
```

... a subsequent call to `getQueryString()` will return:

```sql
SELECT *
FROM foo
WHERE bar = :_1_1_
```

> **Note:**
>
> The first part of the auto-generated placeholder name will increment each time
> a new statement is created; the second part will increment for each inline value
> bound to that statement.

If `$bar_value` is `foo-bar`, calling `getBindValues()` will return:

```php
[
    ':_1_1_' => ['foo-bar', \PDO::PARAM_STR],
]
```

Note that the placeholder is automatically recognized as a string; the same will
be true for nulls, integers, and floats.

If you want to explicitly bind the value as some other type, you can pass that
type after the value:

```php
$select
    ->columns('*')
    ->from('foo')
    ->where('bar = ', $bar_value, \PDO::PARAM_LOB);
```

If you bind an array inline, the _Statement_ will bind each element separately
with its own placeholder, comma-separate the placeholders, and wrap them in
parentheses. This makes using an IN() condition very convenient.

```php
$bar_value = ['foo', 'bar', 'baz'];

// SELECT * FROM foo WHERE bar IN (:_1_1_, :_1_2_, :_1_3_)
$select
    ->columns('*')
    ->from('foo')
    ->where('bar IN ', $bar_value);
```

Finally, if the inline value is itself a _Statement_, it will be converted to
a string via `getQueryString()` and returned surrounded in parentheses:

```php
// SELECT * FROM foo WHERE bar IN (SELECT baz FROM dib)
$select
    ->columns('*')
    ->from('foo')
    ->where('bar IN ', $select->subSelect()
        ->columns('baz')
        ->from('dib')
    );
```

> **Note:**
>
> Any values bound to the sub-statement will be transferred to the main statement.

## `sprintf()` Inline Binding

If you need to bind more than one value into a condition, you can use an
`sprintf` variation of implicit binding. Pass an expression string formatted for
[`sprintf`](https://www.php.net/sprintf) along with the values to bind:

```php
// SELECT * FROM foo WHERE bar BETWEEN :_1_1_ AND :_1_2_
$select
    ->columns('*')
    ->from('foo')
    ->whereSprintf(
        'bar BETWEEN %s AND %s',
        $low_value,
        $high_value
    );
```

Note that you should use only `%s` in the format string, since it is the
*placeholder token* that will be interpolated into the expression, not the
*actual value*.

## Explicit Parameter Binding

You can still use the normal PDO binding approach, where you explicitly set
named parameters in conditions, and then bind the values with a separate call:

```php
$select
    ->columns('*')
    ->from('foo')
    ->where('bar = :bar')
    ->orWhere('baz = :baz')
    ->bindValue('bar', $bar_value);
    ->bindValue('baz', $baz_value);
```

These too will automatically recognize strings, nulls, integers, and floats,
and set the approporate PDO parameter type. If you want to explicitly bind the
value as some other type, pass an optional third parameter to `bindValue()`:

```php
$select
    ->columns('*')
    ->from('foo')
    ->where('bar = :bar')
    ->orWhere('baz = :baz')
    ->bindValue('bar', $bar_value, \PDO::PARAM_LOB);
    ->bindValue('baz', $baz_value);
```

You can also bind multiple values at once ...

```php
$select
    ->columns('*')
    ->from('foo')
    ->where('bar = :bar')
    ->orWhere('baz = :baz')
    ->bindValues([
        'bar' => $bar_value,
        'baz' => $baz_value
    );
```

... but in that case you will not be able to explicitly set the parameter types.

The automatic binding of array elements, as with implicit inline binding, **does
not work** with explicit parameter binding.
