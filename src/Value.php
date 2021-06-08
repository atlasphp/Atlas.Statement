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
    protected mixed $value;

    protected int $type;

    public function __construct(
        mixed $value,
        ?int $type
    ) {
        $this->value = $value;
        $this->setType($type);
    }

    public function getValue() : mixed
    {
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

    protected function setType(?int $type) : void
    {
        if ($type !== null) {
            $this->type = $type;
            return;
        }

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
