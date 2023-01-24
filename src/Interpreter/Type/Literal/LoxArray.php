<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use function abs;
use function array_merge;
use function array_pop;
use function array_push;
use function array_reverse;
use function array_shift;
use function array_slice;
use function array_unshift;
use function implode;

class LoxArray extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return '[' . implode(', ', $this->value) . ']';
    }

    /**
     * @inheritDoc
     */
    protected function getFunctions(): array
    {
        return array_merge(
            parent::getFunctions(),
            [
                'count' => fn() => new LoxNumber(count($this->value)),
                'at' => function ($index = 0) {
                    $index = (int)(string)$index;
                    if ($index < 0) {
                        $index = count($this->value) - abs($index);
                    }

                    return $this->value[$index] ?? new LoxNil();
                },
                'pop' => fn() => array_pop($this->value),
                'push' => function ($value) {
                    if ($value === $this) {
                        return new LoxNil();
                    }

                    return new LoxNumber(array_push($this->value, $value));
                },
                'shift' => fn() => array_shift($this->value),
                'unshift' => function ($value) {
                    if ($value === $this) {
                        return new LoxNil();
                    }

                    return array_unshift($this->value, $value);
                },
                'reverse' => fn() => new LoxArray(array_reverse($this->value)),
                'slice' => function ($start, $length = null) {
                    $start = (int)(string)$start;

                    if ($length !== null) {
                        $length = (int)(string)$length;
                    }

                    return new LoxArray(array_slice($this->value, $start, $length));
                },
            ]
        );
    }
}
