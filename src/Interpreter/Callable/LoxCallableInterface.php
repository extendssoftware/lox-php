<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Callable;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\LoxExceptionInterface;
use Stringable;

interface LoxCallableInterface extends Stringable
{
    /**
     * Call callable.
     *
     * @param InterpreterInterface $interpreter
     * @param array<int, mixed>    $arguments
     *
     * @return mixed
     * @throws LoxExceptionInterface
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed;

    /**
     * Callable argument count.
     *
     * @return int
     */
    public function arity(): int;
}
