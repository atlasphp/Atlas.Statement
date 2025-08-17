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

trait Having
{
    /**
     * @var Component\Conditions
     */
    protected Component\Conditions $having;

    /**
     * @param string $condition
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function having(string $condition, mixed ...$bindInline) : static
    {
        $this->having->and($condition, ...$bindInline);
        return $this;
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function havingSprintf(string $format, mixed ...$bindInline) : static
    {
        $this->having->andSprintf($format, ...$bindInline);
        return $this;
    }

    /**
     * @param string $condition
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function andHaving(string $condition, mixed ...$bindInline) : static
    {
        return $this->having($condition, ...$bindInline);
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function andHavingSprintf(string $format, mixed ...$bindInline) : static
    {
        return $this->havingSprintf($format, ...$bindInline);
    }

    /**
     * @param string $condition
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function orHaving(string $condition, mixed ...$bindInline) : static
    {
        $this->having->or($condition, ...$bindInline);
        return $this;
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function orHavingSprintf(string $format, mixed ...$bindInline) : static
    {
        $this->having->orSprintf($format, ...$bindInline);
        return $this;
    }

    /**
     * @param string $condition
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function catHaving(string $condition, mixed ...$bindInline) : static
    {
        $this->having->cat($condition, ...$bindInline);
        return $this;
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return $this
     */
    public function catHavingSprintf(string $format, mixed ...$bindInline) : static
    {
        $this->having->catSprintf($format, ...$bindInline);
        return $this;
    }

    /**
     * @return $this
     */
    public function resetHaving() : static
    {
        $this->having = new Component\Conditions($this->bind, 'HAVING');
        return $this;
    }
}
