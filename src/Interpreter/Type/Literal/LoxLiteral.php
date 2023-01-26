<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use ExtendsSoftware\LoxPHP\Interpreter\Error\RuntimeError;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\ToString;
use ExtendsSoftware\LoxPHP\Interpreter\Type\LoxInstance;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

abstract class LoxLiteral extends LoxInstance
{
    /**
     * LoxLiteral constructor.
     *
     * @param mixed $value
     */
    public function __construct(protected mixed $value)
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name): mixed
    {
        return match ($name->getLexeme()) {
            'toString' => new ToString($this->value),
            default => parent::get($name),
        };
    }

    /**
     * @inheritDoc
     * @throws RuntimeError
     */
    public function set(TokenInterface $name, mixed $value): void
    {
        throw new RuntimeError("Can't add property to literal.", $name->getLine(), $name->getColumn());
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * Get literal value.
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
