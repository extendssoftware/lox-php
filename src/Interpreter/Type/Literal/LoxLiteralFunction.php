<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use ExtendsSoftware\LoxPHP\Interpreter\Error\RuntimeError;
use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\LoxCallableInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use ReflectionFunction;
use Throwable;

class LoxLiteralFunction implements LoxCallableInterface
{
    /**
     * LoxLiteral constructor.
     *
     * @param TokenInterface     $name
     * @param ReflectionFunction $function
     */
    public function __construct(readonly private TokenInterface $name, readonly private ReflectionFunction $function)
    {
    }

    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed
    {
        try {
            return $this->function->invoke(...$arguments);
        } catch (Throwable $exception) {
            throw new RuntimeError($exception->getMessage(), $this->name->getLine(), $this->name->getColumn());
        }
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
        return '<native function>';
    }
}
