<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Callable;

class LoxNull extends LoxLiteral
{
    /**
     * LoxNull constructor.
     */
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return 'nil';
    }
}
