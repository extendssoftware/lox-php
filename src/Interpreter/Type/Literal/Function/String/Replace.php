<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxString;
use function str_replace;

class Replace extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxString
    {
        return new LoxString(str_replace((string)$arguments[0], (string)$arguments[1], $this->value));
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [2];
    }
}
