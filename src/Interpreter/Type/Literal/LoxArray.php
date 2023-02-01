<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Function\LoxFunction;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use function abs;
use function array_filter;
use function array_map;
use function array_pop;
use function array_push;
use function array_reverse;
use function array_shift;
use function array_slice;
use function array_unshift;
use function current;
use function end;
use function implode;
use function max;
use function min;
use function reset;

class LoxArray extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name): mixed
    {
        return match ($name->getLexeme()) {
            'count' => function (): LoxNumber {
                return new LoxNumber(count($this->value));
            },
            'each' => function (InterpreterInterface $interpreter, $callback): LoxNil {
                if ($callback instanceof LoxFunction) {
                    foreach ($this->value as $index => $value) {
                        $callback->call($interpreter, [$value, new LoxNumber($index)]);
                    }
                }

                return new LoxNil();
            },
            'filter' => function (InterpreterInterface $interpreter, $callback = null): LoxArray {
                $values = array_filter($this->value, function ($value) use ($interpreter, $callback) {
                    if ($callback instanceof LoxFunction) {
                        return $interpreter->isTruthy($callback->call($interpreter, [$value]));
                    } else {
                        return $interpreter->isTruthy($value);
                    }
                });

                return new LoxArray($values);
            },
            'first' => function (): mixed {
                reset($this->value);

                return current($this->value) ?? new LoxNil();
            },
            'get' => function ($index = null): mixed {
                $index = (int)(string)$index;
                if ($index < 0) {
                    $index = count($this->value) - abs($index);
                }

                return $this->value[$index] ?? new LoxNil();
            },
            'implode' => function ($separator = null): LoxString {
                $separator = (string)$separator;

                return new LoxString(implode($separator, $this->value));
            },
            'last' => function (): mixed {
                end($this->value);

                return current($this->value) ?? new LoxNil();
            },
            'map' => function (InterpreterInterface $interpreter, $callback): LoxArray {
                if ($callback instanceof LoxFunction) {
                    $values = array_map(function ($value) use ($interpreter, $callback) {
                        return $callback->call($interpreter, [$value]) ?: $value;
                    }, $this->value);
                }

                return new LoxArray($values ?? $this->value);
            },
            'max' => function (): LoxNumber|LoxNil {
                $values = [];
                foreach ($this->value as $value) {
                    $values[] = (int)(string)$value;
                }

                return $values ? new LoxNumber(max($values)) : new LoxNil();
            },
            'min' => function (): LoxNumber|LoxNil {
                $values = [];
                foreach ($this->value as $value) {
                    $values[] = (int)(string)$value;
                }

                return $values ? new LoxNumber(min($values)) : new LoxNil();
            },
            'pop' => function (): mixed {
                return array_pop($this->value) ?? new LoxNil();
            },
            'push' => function ($value): LoxNumber {
                return new LoxNumber(array_push($this->value, $value));
            },
            'reverse' => function (): LoxArray {
                return new LoxArray(array_reverse($this->value));
            },
            'shift' => function (): mixed {
                return array_shift($this->value) ?? new LoxNil();
            },
            'slice' => function ($start, $length = null) {
                $start = (int)(string)$start;
                if ($length !== null) {
                    $length = (int)(string)$length;
                }

                return new LoxArray(array_slice($this->value, $start, $length));
            },
            'unshift' => function ($value) {
                return new LoxNumber(array_unshift($this->value, $value));
            },
            default => parent::get($name),
        };
    }


    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return '[' . implode(', ', $this->value) . ']';
    }
}
