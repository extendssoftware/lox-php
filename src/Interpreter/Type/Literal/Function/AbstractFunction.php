<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function;

use ExtendsSoftware\LoxPHP\Interpreter\LoxCallableInterface;

abstract class AbstractFunction implements LoxCallableInterface
{
    /**
     * AbstractFunction constructor.
     *
     * @param mixed $value
     */
    public function __construct(protected mixed $value)
    {
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return '<function native>';
    }
}
