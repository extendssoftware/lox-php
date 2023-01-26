<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Function\LoxFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxArray;
use function array_map;

class Map extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed
    {
        $callback = $arguments[0] ?? null;
        if ($callback instanceof LoxFunction) {
            $values = array_map(function ($value) use ($interpreter, $callback) {
                return $callback->call($interpreter, [$value]) ?: $value;
            }, $this->value);
        }

        return new LoxArray($values ?? $this->value);
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [0, 1];
    }
}
