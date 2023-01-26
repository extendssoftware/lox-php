<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNil;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxString;
use function abs;
use function strlen;

class Get extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxString|LoxNil
    {
        $index = (int)(string)$arguments[0];
        if ($index < 0) {
            $index = strlen($this->value) - abs($index);
        }

        if (isset($this->value[$index])) {
            return new LoxString($this->value[$index]);
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
