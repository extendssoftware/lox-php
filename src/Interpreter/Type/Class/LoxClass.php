<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Class;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\LoxCallableInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Function\LoxFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\LoxInstance;

readonly class LoxClass implements LoxCallableInterface
{
    /**
     * LoxClass constructor.
     *
     * @param string $name
     * @param LoxClass|null $superclass
     * @param array<string, LoxFunction> $methods
     */
    public function __construct(
        private string $name,
        private ?LoxClass $superclass,
        private array $methods
    ) {
    }

    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): LoxInstance
    {
        $instance = new LoxInstance($this);
        $this->findMethod('init')?->bind($instance)->call($interpreter, $arguments);

        return $instance;
    }

    /**
     * Find method.
     *
     * @param string $name
     *
     * @return LoxFunction|null
     */
    public function findMethod(string $name): ?LoxFunction
    {
        if (isset($this->methods[$name])) {
            return $this->methods[$name];
        }

        return $this->superclass?->findMethod($name);
    }

    /**
     * @inheritDoc
     */
    public function arities(): array
    {
        return $this->findMethod('init')?->arities() ?: [0];
    }

    /**
     * Get string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
