<?php
/**
 *
 * This file is part of Atlas for PHP.
 *
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 */
declare(strict_types=1);

namespace Atlas\Statement\Clause;

trait ModifyColumns
{
    /**
     * @var Component\ModifyColumns
     */
    protected Component\ModifyColumns $columns;

    /**
     * @param string $column
     * @param mixed  ...$value
     *
     * @return $this
     */
    public function column(string $column, mixed ...$value) : static
    {
        $this->columns->hold($column, ...$value);
        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function columns(array $columns) : static
    {
        foreach ($columns as $key => $val) {
            if (is_int($key)) {
                /** @var string $val */
                $this->column($val);
            } else {
                $this->column($key, $val);
            }
        }

        return $this;
    }

    /**
     * @param string $column
     * @param mixed  $value
     *
     * @return $this
     */
    public function set(string $column, mixed $value) : static
    {
        $this->columns->raw($column, $value);
        return $this;
    }

    /**
     * @return bool
     */
    public function hasColumns() : bool
    {
        return $this->columns->hasAny();
    }

    /**
     * @return $this
     */
    public function resetColumns() : static
    {
        $type = strrchr(static::CLASS, '\\') . 'Columns';
        $class = __NAMESPACE__ . '\\Component' . $type;
        /** @var Component\ModifyColumns $class */
        $class = new $class($this->bind, $this->driver);
        $this->columns = $class;
        return $this;
    }
}
