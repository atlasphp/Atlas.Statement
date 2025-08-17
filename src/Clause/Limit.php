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

trait Limit
{
    /**
     * @var Component\Limit
     */
    protected Component\Limit $limit;

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit) : static
    {
        $this->limit->setLimit($limit);
        return $this;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function offset(int $offset) : static
    {
        $this->limit->setOffset($offset);
        return $this;
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function page(int $page) : static
    {
        $this->limit->setPage($page);
        return $this;
    }

    /**
     * @param int $perPage
     *
     * @return $this
     */
    public function perPage(int $perPage) : static
    {
        $this->limit->setPerPage($perPage);
        return $this;
    }

    /**
     * @return $this
     */
    public function resetLimit() : static
    {
        $limit = $this->driver->getLimitClass();
        /** @var Component\Limit $limit */
        $limit = new $limit();
        $this->limit = $limit;
        return $this;
    }
}
