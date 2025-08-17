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

abstract class Component
{
    /**
     * @param array $list
     *
     * @return string
     */
    public function indentCsv(array $list) : string
    {
        return PHP_EOL . '    '
             . implode(',' . PHP_EOL . '    ', $list);
    }

    /**
     * @param array $list
     *
     * @return string
     */
    public function indent(array $list) : string
    {
        if (empty($list)) {
            return '';
        }

        return PHP_EOL . '    '
             . implode(PHP_EOL . '    ', $list);
    }

    /**
     * @return string
     */
    abstract public function build() : string;
}
