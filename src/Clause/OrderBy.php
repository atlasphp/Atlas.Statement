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

trait OrderBy
{
    /**
     * @var Component\By
     */
    protected Component\By $orderBy;

    /**
     * @param string $expr
     * @param string ...$exprs
     *
     * @return $this
     */
    public function orderBy(string $expr, string ...$exprs) : static
    {
        $this->orderBy->expr($expr, ...$exprs);
        return $this;
    }

    /**
     * @return $this
     */
    public function resetOrderBy() : static
    {
        $this->orderBy = new Component\By('ORDER');
        return $this;
    }
}
