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

class By extends Component
{
    /**
     * @var string
     */
    protected string $type;

    /**
     * @var array<array-key, string>
     */
    protected array $list = [];

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param string $expr
     * @param string ...$exprs
     *
     * @return void
     */
    public function expr(string $expr, string ...$exprs) : void
    {
        $this->list[] = $expr;

        foreach ($exprs as $expr) {
            $this->list[] = $expr;
        }
    }

    /**
     * @return string
     */
    public function build() : string
    {
        if (empty($this->list)) {
            return '';
        }

        return PHP_EOL . $this->type . ' BY' . $this->indentCsv($this->list);
    }
}
