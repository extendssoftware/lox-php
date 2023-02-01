<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Error;

use Exception;
use ExtendsSoftware\LoxPHP\LoxPHPExceptionInterface;
use function sprintf;

class ParseError extends Exception implements LoxPHPExceptionInterface
{
    /**
     * ParseError constructor.
     *
     * @param string $reason
     * @param int    $line
     * @param int    $column
     */
    public function __construct(string $reason, int $line, int $column)
    {
        parent::__construct(
            sprintf(
                'Parse error at line %d and column %d: %s',
                $line,
                $column,
                $reason
            )
        );
    }
}
