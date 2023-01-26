<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNumber;

class Count extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxNumber
    {
        return new LoxNumber(count($this->value));
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [0];
    }
}
