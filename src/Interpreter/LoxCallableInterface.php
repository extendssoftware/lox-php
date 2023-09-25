<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter;

use ExtendsSoftware\LoxPHP\LoxPHPExceptionInterface;

interface LoxCallableInterface
{
    /**
     * Call callable.
     *
     * @param InterpreterInterface $interpreter
     * @param array<int, mixed> $arguments
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed;

    /**
     * Possible arities.
     *
     * @return array<int>
     */
    public function arities(): array;
}
