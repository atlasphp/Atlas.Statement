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

class Value
{
    public function __construct(
        protected mixed $value,
        protected ?int $type
    ) {
        if ($this->type === null) {
            $this->fixType();
        }
    }

    public function getValue() : mixed
    {
        if ($this->type === PDO::PARAM_BOOL && is_bool($this->value)) {
            return $this->value ? '1' : '0';
        }

        return $this->value;
    }

    public function getType() : int
    {
        return $this->type;
    }

    public function asArray() : array
    {
        return [$this->value, $this->type];
    }

    protected function fixType() : void
    {
        if (is_null($this->value)) {
            $this->type = PDO::PARAM_NULL;
            return;
        }

        if (is_bool($this->value)) {
            $this->type = PDO::PARAM_BOOL;
            return;
        }

        if (is_int($this->value)) {
            $this->type = PDO::PARAM_INT;
            return;
        }

        $this->type = PDO::PARAM_STR;
    }
}
