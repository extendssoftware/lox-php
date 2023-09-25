<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP;

interface LoxPHPInterface
{
    /**
     * LoxPHP version.
     */
    public const VERSION = '0.1.0';

    /**
     * Run source code.
     *
     * @param string $source
     *
     * @return void
     * @throws LoxPHPExceptionInterface When failed to run source code.
     */
    public function run(string $source): void;
}
