# Other Topics

## Microsoft SQL Server LIMIT and OFFSET

If the _Statement_ driver is for Microsoft SQL Server ('sqlsrv'), the
LIMIT-related methods on the _Statement_ will generate sqlsrv-specific
variations of `LIMIT ... OFFSET`:

- If only a `LIMIT` is present, it will be translated as a `TOP` clause.

- If both `LIMIT` and `OFFSET` are present, it will be translated as an
  `OFFSET ... ROWS FETCH NEXT ... ROWS ONLY` clause. In this case there *must*
  be an `ORDER BY` clause, as the offset clause is a sub-clause of `ORDER
  BY`.

## Identifier Quoting

You can apply identifier quoting as needed by using the `quoteIdentifier()`
method (available on all _Statement_ objects).

INSERT and UPDATE statements will automatically quote the column name that is
being inserted or updated. No other automatic quoting of identifiers is
applied.

## Table Prefixes

One frequently-requested feature for this package is support for "automatic
table prefixes" on all statements.  This feature sounds great in theory, but in
practice it is (1) difficult to implement well, and (2) even when implemented it
turns out to be not as great as it seems in theory. This assessment is the
result of the hard trials of experience. For those of you who want modifiable
table prefixes, we suggest using constants with your table names prefixed as
desired; as the prefixes change, you can then change your constants.

## Statement Formatting

Each _Statement_ attempts to format its query strings nicely, but it still may
not look "nice enough" in some cases. If you want nicely-formatted SQL, say for
logs or for debugging, consider using [jdorn/sql-formatter][] on the string
returned by `Statement::getQueryString()`.

[jdorn/sql-formatter]: https://packagist.org/packages/jdorn/sql-formatter
