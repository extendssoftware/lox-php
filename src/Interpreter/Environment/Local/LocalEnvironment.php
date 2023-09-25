<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Environment\Local;

use ExtendsSoftware\LoxPHP\Interpreter\Environment\EnvironmentInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Environment\Global\GlobalEnvironment;

class LocalEnvironment extends GlobalEnvironment
{
    /**
     * @inheritDoc
     */
    public function define(string $name, mixed $value): EnvironmentInterface
    {
        $environment = clone $this;
        $environment->values[$name] = $value;

        return $environment;
    }
}
