<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxArray;
use function explode;
use function strlen;

class Explode extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxArray
    {
        $separator = (string)($arguments[0] ?? null);
        if (strlen($separator) === 0) {
            $separator = ' ';
        }

        return new LoxArray(explode($separator, $this->value));
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [0, 1];
    }
}
