<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP;

interface LoxInterface
{
    /**
     * Run source code.
     *
     * @param string $source
     *
     * @return void
     * @throws LoxExceptionInterface When failed to run source code.
     */
    public function run(string $source): void;
}
