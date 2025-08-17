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

use Atlas\Statement\Bind;
use Atlas\Statement\Driver\Driver;

abstract class ModifyColumns extends Component
{
    /**
     * @var array<string, mixed>
     */
    protected array $list = [];

    /**
     * @param Bind   $bind
     * @param Driver $driver
     */
    public function __construct(protected Bind $bind, protected Driver $driver)
    {
    }

    /**
     * @return bool
     */
    public function hasAny() : bool
    {
        return ! empty($this->list);
    }

    /**
     * @param string $column
     * @param mixed  ...$value
     *
     * @return void
     */
    public function hold(string $column, mixed ...$value) : void
    {
        $this->list[$column] = ":{$column}";

        if (! empty($value)) {
            $this->bind->value($column, ...$value);
        }
    }

    /**
     * @param string $column
     * @param mixed  $value
     *
     * @return void
     */
    public function raw(string $column, mixed $value) : void
    {
        if ($value === null) {
            $value = 'NULL';
        }

        $this->list[$column] = $value;
        $this->bind->remove($column);
    }
}
