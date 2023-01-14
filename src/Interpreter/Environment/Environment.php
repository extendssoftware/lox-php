<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Environment;

use ExtendsSoftware\LoxPHP\Interpreter\Error\RuntimeError;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use function array_key_exists;

class Environment implements EnvironmentInterface
{
    /**
     * Environment values.
     *
     * @var array<string, mixed>
     */
    public array $values = [];

    /**
     * Environment constructor.
     *
     * @param EnvironmentInterface|null $enclosing
     */
    public function __construct(readonly private ?EnvironmentInterface $enclosing = null)
    {
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): mixed
    {
        if (array_key_exists($name, $this->values)) {
            return $this->values[$name];
        } elseif ($this->enclosing) {
            return $this->enclosing->get($name);
        } else {
            throw new RuntimeError("Undefined variable '" . $name . "'.", 0, 0);
        }
    }

    /**
     * @inheritDoc
     */
    public function define(string $name, mixed $value): void
    {
        $this->values[$name] = $value;
    }

    /**
     * @inheritDoc
     */
    public function assign(TokenInterface $name, mixed $value): void
    {
        $lexeme = $name->getLexeme();
        if (array_key_exists($lexeme, $this->values)) {
            $this->values[$lexeme] = $value;
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
