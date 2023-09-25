<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type;

use ExtendsSoftware\LoxPHP\Interpreter\Error\RuntimeError;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Class\LoxClass;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

use function array_key_exists;
use function sprintf;

class LoxInstance
{
    /**
     * LoxInstance constructor.
     *
     * @param LoxClass|null $class
     * @param array<string, mixed> $properties
     */
    public function __construct(readonly private ?LoxClass $class = null, private array $properties = [])
    {
    }

    /**
     * Get property value.
     *
     * @param TokenInterface $name
     *
     * @return mixed
     * @throws RuntimeError
     */
    public function get(TokenInterface $name): mixed
    {
        $lexeme = $name->getLexeme();
        if (array_key_exists($lexeme, $this->properties)) {
            return $this->properties[$lexeme];
        }

        $method = $this->class?->findMethod($lexeme);
        if ($method) {
            return $method->bind($this);
        }

        throw new RuntimeError(
            sprintf("Undefined property '%s'.", $lexeme),
            $name->getLine(),
            $name->getColumn()
        );
    }

    /**
     * Set property value.
     *
     * @param TokenInterface $name
     * @param mixed $value
     *
     * @return void
     */
    public function set(TokenInterface $name, mixed $value): void
    {
        $this->properties[(string)$name->getLexeme()] = $value;
    }

    /**
     * Get string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf("<instance %s>", $this->class);
    }
}
