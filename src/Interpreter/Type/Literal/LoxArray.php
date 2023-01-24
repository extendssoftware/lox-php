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
                'count' => fn(): LoxNumber => new LoxNumber(count($this->value)),
                'get' => function (string $index = '0') {
                    $index = (int)$index;
                    if ($index < 0) {
                        $index = count($this->value) - abs($index);
                    }

                    return $this->value[$index] ?? new LoxNil();
                },
                'pop' => fn() => array_pop($this->value),
                'push' => fn($value): LoxNumber => new LoxNumber(array_push($this->value, $value)),
                'shift' => fn() => array_shift($this->value),
                'unshift' => fn($value): LoxNumber => new LoxNumber(array_unshift($this->value, $value)),
                'reverse' => fn(): LoxArray => new LoxArray(array_reverse($this->value)),
                'slice' => function (string $start, string $length = null): LoxArray {
                    $start = (int)$start;

                    if ($length !== null) {
                        $length = (int)$length;
                    }

                    return new LoxArray(array_slice($this->value, $start, $length));
                },
                'implode' => fn(string $separator = ''): LoxString => new LoxString(implode($separator, $this->value)),
            ]
        );
    }
}
