<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Function\LoxFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxArray;
use function array_filter;

class Filter extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxArray
    {
        $callback = $arguments[0] ?? null;
        if ($callback instanceof LoxFunction) {
            $values = array_filter($this->value, function ($value) use ($interpreter, $callback) {
                return $interpreter->isTruthy($callback->call($interpreter, [$value]));
            });
        } else {
            $values = array_filter($this->value);
        }

        return new LoxArray($values);
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [0, 1];
    }
}
