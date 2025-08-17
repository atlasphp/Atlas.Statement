<?php
/**
 *
 * This file is part of Atlas for PHP.
 *
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 */
declare(strict_types=1);

namespace Atlas\Statement\Driver;

use Atlas\Statement\Clause\Component\Limit;

abstract class Driver
{
    /**
     * @return class-string
     */
    public function getLimitClass() : string
    {
        return Limit::CLASS;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    abstract public function quoteIdentifier(string $name) : string;
}
