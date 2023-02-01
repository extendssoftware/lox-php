<?php

namespace ExtendsSoftware\LoxPHP\Resolver\Error;

use Exception;
use ExtendsSoftware\LoxPHP\LoxPHPExceptionInterface;
use function sprintf;

class CompileError extends Exception implements LoxPHPExceptionInterface
{
    /**
     * CompileError constructor.
     *
     * @param string $reason
     * @param int    $line
     * @param int    $column
     */
    public function __construct(string $reason, int $line, int $column)
    {
        parent::__construct(
            sprintf(
                'Compile error at line %d and column %d: %s',
                $line,
                $column,
                $reason
            )
        );
    }
}
