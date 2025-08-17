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
    /**
     * @var mixed
     */
    protected mixed $value;

    /**
     * @var int
     */
    protected int $type;

    /**
     * @param mixed    $value
     * @param int|null $type
     */
    public function __construct(mixed $value, ?int $type)
    {
        $this->value = $value;
        $this->setType($type);
    }

    /**
     * @return mixed
     */
    public function getValue() : mixed
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     *
     * @return void
     */
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
