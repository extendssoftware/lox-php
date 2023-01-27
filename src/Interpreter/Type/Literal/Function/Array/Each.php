<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Function\LoxFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNil;

class Each extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxNil
    {
        $callback = $arguments[0];
        if ($callback instanceof LoxFunction) {
            foreach ($this->value as $value) {
                $callback->call($interpreter, [$value]);
            }
        }

        return new LoxNil();
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [1];
    }
}
