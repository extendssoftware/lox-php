<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter;

use ExtendsSoftware\LoxPHP\Interpreter\Environment\EnvironmentInterface;
use ExtendsSoftware\LoxPHP\LoxPHPExceptionInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;

interface InterpreterInterface
{
    /**
     * Execute statements.
     *
     * @param array<StatementInterface> $statements
     *
     * @return InterpreterInterface
     * @throws LoxPHPExceptionInterface
     */
    public function execute(array $statements): InterpreterInterface;

    /**
     * Execute block of statements.
     *
     * @param array<StatementInterface> $statements
     * @param EnvironmentInterface $environment
     *
     * @return InterpreterInterface
     * @throws LoxPHPExceptionInterface
     */
    public function executeBlock(array $statements, EnvironmentInterface $environment): InterpreterInterface;

    /**
     * Check if value is truthy.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isTruthy(mixed $value): bool;
}
