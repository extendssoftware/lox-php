<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Scanner\Token;

use ExtendsSoftware\LoxPHP\Scanner\Token\Type\TokenType;

use function str_pad;

readonly class Token implements TokenInterface
{
    /**
     * Token constructor.
     *
     * @param TokenType $type
     * @param int $line
     * @param int $column
     * @param mixed $lexeme
     */
    public function __construct(
        private TokenType $type,
        private int $line,
        private int $column,
        private mixed $lexeme
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getType(): TokenType
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @inheritDoc
     */
    public function getColumn(): int
    {
        return $this->column;
    }

    /**
     * @inheritDoc
     */
    public function getLexeme(): mixed
    {
        return $this->lexeme;
    }

    /**
     * Get string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return
            str_pad((string)$this->line, 5) .
            str_pad((string)$this->column, 7) .
            str_pad($this->type->name, 14) .
            $this->lexeme;
    }
}
