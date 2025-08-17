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

trait Where
{
    /**
     * @var Component\Conditions
     */
    protected Component\Conditions $where;

    /**
     * @param string $condition
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function where(string $condition, mixed ...$bindInline) : static
    {
        $this->where->and($condition, ...$bindInline);
        return $this;
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function whereSprintf(string $format, mixed ...$bindInline) : static
    {
        $this->where->andSprintf($format, ...$bindInline);
        return $this;
    }

    /**
     * @param string $condition
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function andWhere(string $condition, mixed ...$bindInline) : static
    {
        return $this->where($condition, ...$bindInline);
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function andWhereSprintf(string $format, mixed ...$bindInline) : static
    {
        return $this->whereSprintf($format, ...$bindInline);
    }

    /**
     * @param string $condition
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function orWhere(string $condition, mixed ...$bindInline) : static
    {
        $this->where->or($condition, ...$bindInline);
        return $this;
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function orWhereSprintf(string $format, mixed ...$bindInline) : static
    {
        $this->where->orSprintf($format, ...$bindInline);
        return $this;
    }

    /**
     * @param string $condition
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function catWhere(string $condition, mixed ...$bindInline) : static
    {
        $this->where->cat($condition, ...$bindInline);
        return $this;
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function catWhereSprintf(string $format, mixed ...$bindInline) : static
    {
        $this->where->catSprintf($format, ...$bindInline);
        return $this;
    }

    /**
     * @param array $columnsValues
     *
     * @return $this
     */
    public function whereEquals(array $columnsValues) : static
    {
        foreach ($columnsValues as $key => $val) {
            if (is_numeric($key)) {
                /** @var string $val */
                $this->where($val);
            } elseif ($val === null) {
                $this->where("{$key} IS NULL");
            } elseif ($val === []) {
                $this->where('FALSE');
            } elseif (is_array($val)) {
                $this->where("{$key} IN ", $val);
            } else {
                $this->where("{$key} = ", $val);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function resetWhere() : static
    {
        $this->where = new Component\Conditions($this->bind, 'WHERE');
        return $this;
    }
}
