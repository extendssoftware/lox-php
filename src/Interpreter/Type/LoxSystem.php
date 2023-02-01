<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type;

use Closure;
use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNumber;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use function microtime;
use function round;
use function time;

class LoxSystem extends LoxInstance
{
    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name): Closure
    {
        return match ($name->getLexeme()) {
            'time' => function (InterpreterInterface $interpreter, $milliseconds = null): LoxNumber {
                if ($milliseconds && $interpreter->isTruthy($milliseconds)) {
                    $time = round(microtime(true) * 1000);
                } else {
                    $time = time();
                }

                return new LoxNumber($time);
            },
            default => parent::get($name),
        };
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return 'System';
    }
}
