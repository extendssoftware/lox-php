<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

use function abs;
use function ceil;
use function floor;
use function round;

class LoxNumber extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name, ?bool $nullSafe = null): mixed
    {
        return match ($name->getLexeme()) {
            'abs' => function (): LoxNumber {
                return new LoxNumber(abs($this->value));
            },
            'ceil' => function (): LoxNumber {
                return new LoxNumber(ceil($this->value));
            },
            'floor' => function (): LoxNumber {
                return new LoxNumber(floor($this->value));
            },
            'round' => function (): LoxNumber {
                return new LoxNumber(round($this->value));
            },
            default => parent::get($name, $nullSafe),
        };
    }
}
