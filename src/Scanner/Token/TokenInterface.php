<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Scanner\Token;

use ExtendsSoftware\LoxPHP\Scanner\Token\Type\TokenType;
use Stringable;

interface TokenInterface extends Stringable
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
     * @return string
     */
    public function getLexeme(): string;
}
