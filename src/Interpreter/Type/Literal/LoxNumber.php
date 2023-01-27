<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Number\Abs;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Number\Ceil;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Number\Floor;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Number\Round;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

class LoxNumber extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name): mixed
    {
        $value = $this->value;
        return match ($name->getLexeme()) {
            'abs' => new Abs($value),
            'ceil' => new Ceil($value),
            'floor' => new Floor($value),
            'round' => new Round($value),
            default => parent::get($name),
        };
    }
}
