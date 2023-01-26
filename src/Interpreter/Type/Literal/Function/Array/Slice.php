<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxArray;
use function array_slice;

class Slice extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxArray
    {
        $start = (int)(string)$arguments[0];

        $length = null;
        if (isset($arguments[1])) {
            $length = (int)(string)$arguments[1];
        }

        return new LoxArray(array_slice($this->value, $start, $length));
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [1, 2];
    }
}
