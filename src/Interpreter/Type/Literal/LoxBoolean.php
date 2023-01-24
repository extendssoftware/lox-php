<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

class LoxBoolean extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->value ? '1' : '0';
    }
}
