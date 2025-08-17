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
use Atlas\Statement\Statement;

use function array_key_last;

class From extends Component
{
    /**
     * @var array<array-key, array<array-key, string>>
     */
    protected array $list = [];

    /**
     * @param Bind $bind
     */
    public function __construct(protected Bind $bind)
    {
    }

    /**
     * @param string|Statement $ref
     *
     * @return void
     */
    public function table(string|Statement $ref) : void
    {
        if ($ref instanceof Statement) {
            $this->bind->merge($ref->getBindValueObjects());
            $ref = $ref->getQueryString();
        }

        $this->list[] = [$ref];
    }

    /**
     * @param string           $join
     * @param string|Statement $ref
     * @param string           $condition
     * @param mixed            ...$bindInline
     *
     * @return void
     */
    public function join(
        string $join,
        string|Statement $ref,
        string $condition = '',
        mixed ...$bindInline
    ) : void
    {
        if ($ref instanceof Statement) {
            $this->bind->merge($ref->getBindValueObjects());
            $ref = $ref->getQueryString();
        }

        $condition = ltrim($condition);

        if (
            $condition !== ''
            && strtoupper(substr($condition, 0, 3)) !== 'ON '
            && strtoupper(substr($condition, 0, 6)) !== 'USING '
        ) {
            $condition = 'ON ' . $condition;
        }

        if (! empty($bindInline)) {
            $condition .= $this->bind->inline(...$bindInline);
        }

        $end = array_key_last($this->list);
        $this->list[$end][] = "    {$join} {$ref} {$condition}";
    }

    /**
     * @param string $expr
     * @param mixed  ...$bindInline
     *
     * @return void
     */
    public function catJoin(string $expr, mixed ...$bindInline) : void
    {
        if (! empty($bindInline)) {
            $expr .= $this->bind->inline(...$bindInline);
        }

        $end = array_key_last($this->list);
        $key = array_key_last($this->list[$end]);
        $this->list[$end][$key] .= $expr;
    }

    /**
     * @return string
     */
    public function build() : string
    {
        if (empty($this->list)) {
            return '';
        }

        $from = [];

        foreach ($this->list as $list) {
            $from[] = array_shift($list) . $this->indent($list);
        }

        return PHP_EOL . 'FROM' . $this->indentCsv($from);
    }
}
