<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\LoxCallableInterface;
use ReflectionFunction;

class LoxLiteralFunction implements LoxCallableInterface
{
    /**
     * LoxLiteral constructor.
     *
     * @param LoxLiteral         $literal
     * @param ReflectionFunction $function
     */
    public function __construct(readonly private LoxLiteral $literal, readonly private ReflectionFunction $function)
    {
    }

    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed
    {
        return $this->function->invoke($this->literal, ...$arguments);
    }

    /**
     * @inheritDoc
     */
    public function arity(): int
    {
        return $this->function->getNumberOfParameters();
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return sprintf('<function %s>', $this->function->getName());
    }
}
