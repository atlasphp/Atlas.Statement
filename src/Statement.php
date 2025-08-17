<?php
/**
 *
 * This file is part of Atlas for PHP.
 *
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 */
declare(strict_types=1);

namespace Atlas\Statement;

use Atlas\Statement\Driver\Driver;
use Atlas\Statement\Clause\Component\Flags;
use Atlas\Statement\Clause\Component\With;
/**
 * @phpstan-type valueArray array<string, Value>
 */
abstract class Statement
{
    /**
     * @param string $driverName
     *
     * @return static
     */
    static public function new(string $driverName) : static
    {
        $driver = 'Atlas\\Statement\\Driver\\'
            . ucfirst($driverName)
            . 'Driver';

        return new static(new $driver());
    }

    /**
     * @var Driver
     */
    protected Driver $driver;

    /**
     * @var Bind
     */
    protected Bind $bind;

    /**
     * @var Flags
     */
    protected Flags $flags;

    /**
     * @var With
     */
    protected With $with;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
        $this->bind = new Bind();
        $this->reset();
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $vars = get_object_vars($this);

        foreach ($vars as $name => $prop) {
            if (is_object($prop)) {
                $this->$name = clone $prop;
            }
        }
    }

    /**
     * @param mixed    $value
     * @param int|null $type
     *
     * @return string
     */
    public function bindInline(mixed $value, ?int $type = null) : string
    {
        return $this->bind->inline($value, $type);
    }

    /**
     * @param string $format
     * @param mixed  ...$values
     *
     * @return string
     */
    public function bindSprintf(string $format, mixed ...$values) : string
    {
        return $this->bind->sprintf($format, ...$values);
    }

    /**
     * @param string   $key
     * @param mixed    $value
     * @param int|null $type
     *
     * @return $this
     */
    public function bindValue(string $key, mixed $value, ?int $type = null) : static
    {
        $this->bind->value($key, $value, $type);
        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function bindValues(array $values) : static
    {
        $this->bind->values($values);
        return $this;
    }

    /**
     * @return valueArray
     */
    public function getBindValueObjects() : array
    {
        return $this->bind->getValues();
    }

    /**
     * @return array
     */
    public function getBindValueArrays() : array
    {
        $values = [];

        foreach ($this->bind->getValues() as $name => $value) {
            $values[$name] = [$value->getValue(), $value->getType()];
        }

        return $values;
    }

    /**
     * @param string $flag
     * @param bool   $enable
     *
     * @return void
     */
    public function setFlag(string $flag, bool $enable = true) : void
    {
        $this->flags->set($flag, $enable);
    }

    /**
     * @return $this
     */
    public function reset() : static
    {
        foreach (get_class_methods($this) as $method) {
            if (substr($method, 0, 5) == 'reset' && $method != 'reset') {
                $this->$method();
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function resetFlags() : static
    {
        $this->flags = new Flags();
        return $this;
    }

    /**
     * @return $this
     */
    public function resetWith() : static
    {
        $this->with = new With($this->bind, $this->driver);
        return $this;
    }

    /**
     * @param string           $cteName
     * @param string|Statement $cteStatement
     *
     * @return $this
     */
    public function with(string $cteName, string|Statement $cteStatement) : static
    {
        $this->with->setCte($cteName, [], $cteStatement);
        return $this;
    }

    /**
     * @param string           $cteName
     * @param string[]         $cteColumns
     * @param string|Statement $cteStatement
     *
     * @return $this
     */
    public function withColumns(string $cteName, array $cteColumns, string|Statement $cteStatement) : static
    {
        $this->with->setCte($cteName, $cteColumns, $cteStatement);
        return $this;
    }

    /**
     * @param bool $recursive
     *
     * @return $this
     */
    public function withRecursive(bool $recursive = true) : static
    {
        $this->with->setRecursive($recursive);
        return $this;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function quoteIdentifier(string $name) : string
    {
        return $this->driver->quoteIdentifier($name);
    }

    /**
     * @return string
     */
    abstract public function getQueryString() : string;
}
