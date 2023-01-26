<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxString;

class ToString extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxString
    {
        return new LoxString((string)$this->value);
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [0];
    }
}
