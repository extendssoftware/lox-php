<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxString;
use function implode;

class Implode extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxString
    {
        $separator = (string)($arguments[0] ?? ' ');

        return new LoxString(implode($separator, $this->value));
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [0, 1];
    }
}
