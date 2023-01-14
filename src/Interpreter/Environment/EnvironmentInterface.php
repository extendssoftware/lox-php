<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Environment;

use ExtendsSoftware\LoxPHP\Interpreter\Error\RuntimeError;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

interface EnvironmentInterface
{
    /**
     * Get environment variable.
     *
     * @param string $name
     *
     * @return mixed
     * @throws RuntimeError Undefined variable.
     */
    public function get(string $name): mixed;

    /**
     * Define environment variable.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function define(string $name, mixed $value): void;

    /**
     * Assign existing environment variable.
     *
     * @param TokenInterface $name
     * @param mixed          $value
     *
     * @return void
     * @throws RuntimeError Undefined variable.
     */
    public function assign(TokenInterface $name, mixed $value): void;

    /**
     * Get enclosing environment.
     *
     * @return EnvironmentInterface|null
     */
    public function getEnclosing(): ?EnvironmentInterface;
}
