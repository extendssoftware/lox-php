<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\AbstractFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxArray;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxString;
use function array_map;
use function preg_match;

class PregMatch extends AbstractFunction
{
    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxArray
    {
        @preg_match((string)$arguments[0], $this->value, $matches);

        return new LoxArray(array_map(fn($match) => new LoxString($match), $matches ?: []));
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return [1];
    }
}
