<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNil;
use function current;
use function end;

class Last extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed
    {
        end($this->value);

        return current($this->value) ?? new LoxNil();
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [0];
    }
}
