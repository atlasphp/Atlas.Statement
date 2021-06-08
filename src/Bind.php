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

class Bind
{
    static protected int $instanceCount = 0;

    protected int $inlineCount = 0;

    protected int $inlinePrefix = 0;

    protected array $values = [];

    public function __construct()
    {
        $this->incrementInstanceCount();
    }

    public function __clone()
    {
        $this->incrementInstanceCount();
    }

    protected function incrementInstanceCount() : void
    {
        static::$instanceCount ++;
        $this->inlinePrefix = static::$instanceCount;
    }

    public function reset() : void
    {
        $this->inlineCount = 0;
        $this->values = [];
    }

    public function merge(array $values) : void
    {
        $this->values += $values;
    }

    public function value(string $key, mixed $value, ?int $type = null) : void
    {
        $this->values[$key] = new Value($value, $type);
    }

    public function values(array $values, ?int $type = null) : void
    {
        foreach ($values as $key => $value) {
            $this->value($key, $value, $type);
        }
    }

    public function getValues() : array
    {
        return $this->values;
    }

    public function remove(string $key) : void
    {
        unset($this->values[$key]);
    }

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

    protected function inlineArray(array $array, ?int $type) : string
    {
        $keys = [];

        foreach ($array as $value) {
            $key = $this->inlineValue($value, $type);
            $keys[] = ":{$key}";
        }

        return '(' . implode(', ', $keys) . ')';
    }

    protected function inlineValue(mixed $value, ?int $type) : string
    {
        $this->inlineCount ++;
        $key = "_{$this->inlinePrefix}_{$this->inlineCount}_";
        $this->value($key, $value, $type);
        return $key;
    }

    public function sprintf(string $format, mixed ...$values) : string
    {
        $tokens = [];

        foreach ($values as $value) {
            $tokens[] = $this->inline($value);
        }

        return sprintf($format, ...$tokens);
    }
}
