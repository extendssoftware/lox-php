<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use Closure;
use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\LoxCallableInterface;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;

use function array_splice;

readonly class LiteralFunction implements LoxCallableInterface
{
    /**
     * LiteralFunction constructor.
     *
     * @param Closure $function
     */
    public function __construct(private Closure $function)
    {
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed
    {
        $reflection = new ReflectionFunction($this->function);
        foreach ($reflection->getParameters() as $index => $parameter) {
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && $type->getName() === InterpreterInterface::class) {
                array_splice($arguments, $index, 0, [$interpreter]);
            }
        }

        return $reflection->invoke(...$arguments);
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function arities(): array
    {
        $arities = [];
        $count = 0;

        $reflection = new ReflectionFunction($this->function);
        foreach ($reflection->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && $type->getName() === InterpreterInterface::class) {
                continue;
            }

            if ($parameter->isVariadic()) {
                return [];
            }

            if ($parameter->isOptional()) {
                $arities[] = $count;
            }

            $count++;
        }

        $arities[] = $count;

        return $arities;
    }

    /**
     * Get string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return '<native function>';
    }
}
