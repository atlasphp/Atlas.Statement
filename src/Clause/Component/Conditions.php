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

use function array_key_last;

class Conditions extends Component
{
    /**
     * @var array<array-key, string>
     */
    protected array $list = [];

    /**
     * @param Bind   $bind
     * @param string $type
     */
    public function __construct(protected Bind $bind, protected string $type)
    {
    }

    /**
     * @param string $expr
     * @param mixed  ...$bindInline
     *
     * @return void
     */
    public function and(string $expr, mixed ...$bindInline) : void
    {
        $this->append('AND ', $expr, $bindInline);
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return void
     */
    public function andSprintf(string $format, mixed ...$bindInline) : void
    {
        $this->and($this->bind->sprintf($format, ...$bindInline));
    }

    /**
     * @param string $expr
     * @param mixed  ...$bindInline
     *
     * @return void
     */
    public function or(string $expr, mixed ...$bindInline) : void
    {
        $this->append('OR ', $expr, $bindInline);
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return void
     */
    public function orSprintf(string $format, mixed ...$bindInline) : void
    {
        $this->or($this->bind->sprintf($format, ...$bindInline));
    }

    /**
     * @param string $expr
     * @param mixed  ...$bindInline
     *
     * @return void
     */
    public function cat(string $expr, mixed ...$bindInline) : void
    {
        if (! empty($bindInline)) {
            $expr .= $this->bind->inline(...$bindInline);
        }

        if (empty($this->list)) {
            $this->list[] = '';
        }

        $key = array_key_last($this->list);
        $this->list[$key] .= $expr;
    }

    /**
     * @param string $format
     * @param mixed  ...$bindInline
     *
     * @return void
     */
    public function catSprintf(string $format, mixed ...$bindInline) : void
    {
        $this->cat($this->bind->sprintf($format, ...$bindInline));
    }

    /**
     * @param string $andor
     * @param string $expr
     * @param array  $bindInline
     *
     * @return void
     */
    protected function append(
        string $andor,
        string $expr,
        array $bindInline
    ) : void
    {
        if (! empty($bindInline)) {
            $expr .= $this->bind->inline(...$bindInline);
        }

        if (empty($this->list)) {
            $andor = '';
        }

        $this->list[] = $andor . $expr;
    }

    /**
     * @return string
     */
    public function build() : string
    {
        if (empty($this->list)) {
            return '';
        }

        return PHP_EOL . $this->type . $this->indent($this->list);
    }
}
