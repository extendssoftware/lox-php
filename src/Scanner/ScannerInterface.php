<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Scanner;

use ExtendsSoftware\LoxPHP\Scanner\Error\SyntaxError;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

interface ScannerInterface
{
    /**
     * Process scanner.
     *
     * @param string $source
     *
     * @return array<TokenInterface>
     * @throws SyntaxError When failed to scan source.
     */
    public function scan(string $source): array;
}
