<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Callable;

use Closure;
use ExtendsSoftware\LoxPHP\Interpreter\Error\RuntimeError;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use ReflectionException;
use ReflectionFunction;

abstract class LoxLiteral extends LoxInstance
{
    /**
     * LoxLiteral constructor.
     *
     * @param mixed $value
     */
    public function __construct(readonly protected mixed $value)
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function get(TokenInterface $name): mixed
    {
        $lexeme = $name->getLexeme();
        $functions = $this->getFunctions();
        if (isset($functions[$lexeme])) {
            return new LoxLiteralFunction($this, new ReflectionFunction($functions[$lexeme]));
        }

        return parent::get($name);
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

    /**
     * Get literal functions.
     *
     * @return array<string, Closure>
     */
    protected function getFunctions(): array
    {
        return [
            'toString' => fn() => (string)$this,
        ];
    }
}
