<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNil;
use function abs;
use function count;

class Get extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed
    {
        $index = (int)(string)$arguments[0];
        if ($index < 0) {
            $index = count($this->value) - abs($index);
        }

        return $this->value[$index] ?? new LoxNil();
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [1];
    }
}
