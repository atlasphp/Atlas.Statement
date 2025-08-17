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

use PDO;

/**
 * @phpstan-import-type valueArray from Statement
 */
class Bind
{
    /**
     * @var int
     */
    static protected int $instanceCount = 0;

    /**
     * @var int
     */
    protected int $inlineCount = 0;

    /**
     * @var int
     */
    protected int $inlinePrefix = 0;

    /**
     * @var valueArray
     */
    protected array $values = [];

    /**
     *
     */
    public function __construct()
    {
        $this->incrementInstanceCount();
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->incrementInstanceCount();
    }

    /**
     * @return void
     */
    protected function incrementInstanceCount() : void
    {
        static::$instanceCount ++;
        $this->inlinePrefix = static::$instanceCount;
    }

    /**
     * @return void
     */
    public function reset() : void
    {
        $this->inlineCount = 0;
        $this->values = [];
    }

    /**
     * @param valueArray $values
     *
     * @return void
     */
    public function merge(array $values) : void
    {
        $this->values += $values;
    }

    /**
     * @param string   $key
     * @param mixed    $value
     * @param int|null $type
     *
     * @return void
     */
    public function value(string $key, mixed $value, ?int $type = null) : void
    {
        $this->values[$key] = new Value($value, $type);
    }

    /**
     * @param array    $values
     * @param int|null $type
     *
     * @return void
     */
    public function values(array $values, ?int $type = null) : void
    {
        foreach ($values as $key => $value) {
            $this->value($key, $value, $type);
        }
    }

    /**
     * @return array<string, Value>
     */
    public function getValues() : array
    {
        return $this->values;
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function remove(string $key) : void
    {
        unset($this->values[$key]);
    }

    /**
     * @param mixed    $value
     * @param int|null $type
     *
     * @return string
     */
    public function inline(mixed $value, ?int $type = null) : string
    {
        if ($value instanceof Statement) {
            $this->values += $value->getBindValueObjects();
            return '(' . $value->getQueryString() . ')';
        }

        if (is_array($value)) {
            return $this->inlineArray($value, $type);
        }

        $key = $this->inlineValue($value, $type);
        return ":{$key}";
    }

    /**
     * @param array    $array
     * @param int|null $type
     *
     * @return string
     */
    protected function inlineArray(array $array, ?int $type) : string
    {
        $keys = [];

        foreach ($array as $value) {
            $key = $this->inlineValue($value, $type);
            $keys[] = ":{$key}";
        }

        return '(' . implode(', ', $keys) . ')';
    }

    /**
     * @param mixed    $value
     * @param int|null $type
     *
     * @return string
     */
    protected function inlineValue(mixed $value, ?int $type) : string
    {
        $this->inlineCount ++;
        $key = "_{$this->inlinePrefix}_{$this->inlineCount}_";
        $this->value($key, $value, $type);
        return $key;
    }

    /**
     * @param string $format
     * @param mixed  ...$values
     *
     * @return string
     */
    public function sprintf(string $format, mixed ...$values) : string
    {
        $tokens = [];

        foreach ($values as $value) {
            $tokens[] = $this->inline($value);
        }

        return sprintf($format, ...$tokens);
    }
}
