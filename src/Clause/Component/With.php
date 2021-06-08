<?php
/**
 *
 * This file is part of Atlas for PHP.
 *
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 */
declare(strict_types=1);

namespace Atlas\Statement\Clause\Component;

use Atlas\Statement\Driver\Driver;
use Atlas\Statement\Bind;
use Atlas\Statement\Statement;

class With extends Component
{
    protected array $ctes = [];

    protected bool $recursive = false;

    public function __construct(protected Bind $bind, protected Driver $driver)
    {
    }

    public function setCte(string $name, array $columns, mixed $statement) : void
    {
        $this->ctes[$name] = [$columns, $statement];
    }

    public function setRecursive(bool $recursive) : void
    {
        $this->recursive = $recursive;
    }

    public function build() : string
    {
        if (empty($this->ctes)) {
            return '';
        }

        $ctes = [];

        foreach ($this->ctes as $name => $info) {
            list($columns, $statement) = $info;
            $ctes[] = $this->buildCte($name, $columns, $statement);
        }

        return ($this->recursive ? 'WITH RECURSIVE' : 'WITH')
            . $this->indentCsv($ctes)
            . PHP_EOL;
    }

    protected function buildCte(string $name, array $columns, string|Statement $statement) : string
    {
        $sql = $this->driver->quoteIdentifier($name);

        foreach ($columns as $key => $column) {
            $columns[$key] = $this->driver->quoteIdentifier($column);
        }

        if (! empty($columns)) {
            $sql .= ' (' . implode(', ', $columns) . ')';
        }

        if ($statement instanceof Statement) {
            $this->bind->merge($statement->getBindValues());
            $statement = $statement->getQueryString();
        }

        $sql .= " AS (" . PHP_EOL . "    {$statement}" . PHP_EOL . ")";

        return $sql;
    }
}
