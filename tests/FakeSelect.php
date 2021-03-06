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

class FakeSelect extends Select
{
    public function __get($key)
    {
        return $this->$key;
    }
}
