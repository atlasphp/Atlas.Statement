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

class UpdateColumns extends ModifyColumns
{
    /**
     * @return string
     */
    public function build() : string
    {
        $values = array();

        foreach ($this->list as $column => $value) {
            $quotedColumn = $this->driver->quoteIdentifier($column);
            /** @var int|string $value */
            $values[] = "{$quotedColumn} = {$value}";
        }

        return PHP_EOL . 'SET' . $this->indentCsv($values);
    }
}
