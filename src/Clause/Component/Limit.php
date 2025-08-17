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

class Limit
{
    /**
     * @var int
     */
    protected int $limit = 0;

    /**
     * @var int
     */
    protected int $offset = 0;

    /**
     * @var int
     */
    protected int $page = 0;

    /**
     * @var int
     */
    protected int $perPage = 10;

    /**
     * @param int $limit
     *
     * @return void
     */
    public function setLimit(int $limit) : void
    {
        $this->limit = $limit;

        if ($this->page) {
            $this->page = 0;
            $this->offset = 0;
        }
    }

    /**
     * @return int
     */
    public function getLimit() : int
    {
        return $this->limit;
    }

    /**
     * @param int $offset
     *
     * @return void
     */
    public function setOffset(int $offset) : void
    {
        $this->offset = $offset;

        if ($this->page) {
            $this->page = 0;
            $this->limit = 0;
        }
    }

    /**
     * @return int
     */
    public function getOffset() : int
    {
        return $this->offset;
    }

    /**
     * @param int $page
     *
     * @return void
     */
    public function setPage(int $page) : void
    {
        $this->page = $page;
        $this->setPagingLimitOffset();
    }

    /**
     * @return int
     */
    public function getPage() : int
    {
        return $this->page;
    }

    /**
     * @param int $perPage
     *
     * @return void
     */
    public function setPerPage(int $perPage) : void
    {
        $this->perPage = $perPage;

        if ($this->page) {
            $this->setPagingLimitOffset();
        }
    }

    /**
     * @return int
     */
    public function getPerPage() : int
    {
        return $this->perPage;
    }

    /**
     * @return void
     */
    protected function setPagingLimitOffset() : void
    {
        $this->limit = 0;
        $this->offset = 0;

        if ($this->page) {
            $this->limit = $this->perPage;
            $this->offset = $this->perPage * ($this->page - 1);
        }
    }

    /**
     * @return string
     */
    public function buildEarly() : string
    {
        return '';
    }

    /**
     * @return string
     */
    public function build() : string
    {
        $clause = '';

        if ($this->limit != 0) {
            $clause .= "LIMIT {$this->limit}";
        }

        if ($this->offset != 0) {
            $clause .= " OFFSET {$this->offset}";
        }

        if ($clause != '') {
            $clause = PHP_EOL . ltrim($clause);
        }

        return $clause;
    }
}
