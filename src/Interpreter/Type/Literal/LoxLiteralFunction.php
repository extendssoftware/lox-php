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
     * @param ReflectionFunction $function
     */
    public function __construct(readonly private ReflectionFunction $function)
    {
    }

    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed
    {
        return $this->function->invoke(...$arguments);
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        $count = 0;
        $arities = [];
        foreach ($this->function->getParameters() as $parameter) {
            if ($parameter->isOptional()) {
                $arities[] = $count;
            }

            $count++;
        }

        $arities[] = $count;

        return $arities;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return sprintf('<function %s>', $this->function->getName());
    }
}
