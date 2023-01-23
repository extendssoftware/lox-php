<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

class LoxNil extends LoxLiteral
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
