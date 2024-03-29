<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Scanner\Token;

use ExtendsSoftware\LoxPHP\Scanner\Token\Type\TokenType;

interface TokenInterface
{
    /**
     * Get token type.
     *
     * @return TokenType
     */
    public function getType(): TokenType;

    /**
     * Get line number.
     *
     * @return int
     */
    public function getLine(): int;

    /**
     * Get column number.
     *
     * @return int
     */
    public function getColumn(): int;

    /**
     * Get lexeme.
     *
     * @return mixed
     */
    public function getLexeme(): mixed;
}
