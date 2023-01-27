<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNil;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNumber;
use function min;

class Min extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxNumber|LoxNil
    {
        $values = [];
        foreach ($this->value as $value) {
            $values[] = (int)(string)$value;
        }

        return $values ? new LoxNumber(min($values)) : new LoxNil();
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [0];
    }
}
