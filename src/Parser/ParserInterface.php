<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser;

use ExtendsSoftware\LoxPHP\Parser\Error\ParseError;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

interface ParserInterface
{
    /**
     * Parse tokens.
     *
     * @param array<int, TokenInterface> $tokens
     *
     * @return array<StatementInterface>
     * @throws ParseError When failed to parse.
     */
    public function parse(array $tokens): array;
}
