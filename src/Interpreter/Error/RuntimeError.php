<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Error;

use Exception;
use ExtendsSoftware\LoxPHP\LoxExceptionInterface;
use function sprintf;

class RuntimeError extends Exception implements LoxExceptionInterface
{
    /**
     * RuntimeError constructor.
     *
     * @param string $reason
     * @param int    $line
     * @param int    $column
     */
    public function __construct(string $reason, int $line, int $column)
    {
        parent::__construct(
            sprintf(
                'Runtime error at line %d and column %d: %s',
                $line,
                $column,
                $reason
            )
        );
    }
}
