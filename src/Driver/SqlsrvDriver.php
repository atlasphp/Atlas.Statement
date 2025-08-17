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

use Atlas\Statement\Clause\Component\LimitSqlsrv;

class SqlsrvDriver extends Driver
{
    /**
     * @return class-string
     */
    public function getLimitClass() : string
    {
        return LimitSqlsrv::CLASS;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function quoteIdentifier(string $name) : string
    {
        return "[{$name}]";
    }
}
