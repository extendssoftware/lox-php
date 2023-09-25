<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Environment\Global;

use ExtendsSoftware\LoxPHP\Interpreter\Environment\EnvironmentInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Error\RuntimeError;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

use function array_key_exists;

class GlobalEnvironment implements EnvironmentInterface
{
    /**
     * Environment constructor.
     *
     * @param EnvironmentInterface|null $enclosing
     * @param array<string, mixed> $values
     */
    public function __construct(
        readonly private ?EnvironmentInterface $enclosing = null,
        protected array $values = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): mixed
    {
        if (array_key_exists($name, $this->values)) {
            return $this->values[$name];
        }

        if ($this->enclosing) {
            return $this->enclosing->get($name);
        }

        throw new RuntimeError("Undefined variable '" . $name . "'.", 0, 0);
    }

    /**
     * @inheritDoc
     */
    public function define(string $name, mixed $value): EnvironmentInterface
    {
        $this->values[$name] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function assign(TokenInterface $name, mixed $value): void
    {
        $lexeme = $name->getLexeme();
        if (array_key_exists($lexeme, $this->values)) {
            $this->values[(string)$lexeme] = $value;
        } elseif ($this->enclosing) {
            $this->enclosing->assign($name, $value);
        } else {
            throw new RuntimeError("Undefined variable '" . $lexeme . "'.", $name->getLine(), $name->getColumn());
        }
    }

    /**
     * @inheritDoc
     */
    public function getEnclosing(): ?EnvironmentInterface
    {
        return $this->enclosing;
    }
}
