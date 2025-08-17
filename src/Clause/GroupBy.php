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

trait GroupBy
{
    /**
     * @var Component\By
     */
    protected Component\By $groupBy;

    /**
     * @return $this
     */
    public function resetGroupBy() : static
    {
        $this->groupBy = new Component\By('GROUP');
        return $this;
    }

    /**
     * @param string $expr
     * @param string ...$exprs
     *
     * @return $this
     */
    public function groupBy(string $expr, string ...$exprs) : static
    {
        $this->groupBy->expr($expr, ...$exprs);
        return $this;
    }
}
