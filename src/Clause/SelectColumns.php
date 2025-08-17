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

trait SelectColumns
{
    /**
     * @var Component\SelectColumns
     */
    protected Component\SelectColumns $columns;

    /**
     * @param string $expr
     * @param string ...$exprs
     *
     * @return $this
     */
    public function columns(string $expr, string ...$exprs) : static
    {
        $this->columns->add($expr, ...$exprs);
        return $this;
    }

    /**
     * @return $this
     */
    public function resetColumns() : static
    {
        $this->columns = new Component\SelectColumns();
        return $this;
    }

    /**
     * @return bool
     */
    public function hasColumns() : bool
    {
        return $this->columns->hasAny();
    }
}
