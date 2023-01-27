<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Count;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Each;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Filter;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Get;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Implode;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Map;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Pop;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Push;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Reverse;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Shift;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Slice;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\Array\Unshift;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use function implode;

class LoxArray extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name): mixed
    {
        $value = $this->value;
        return match ($name->getLexeme()) {
            'count' => new Count($value),
            'each' => new Each($value),
            'filter' => new Filter($value),
            'get' => new Get($value),
            'implode' => new Implode($value),
            'map' => new Map($value),
            'pop' => new Pop($value),
            'push' => new Push($value),
            'reverse' => new Reverse($value),
            'shift' => new Shift($value),
            'slice' => new Slice($value),
            'unshift' => new Unshift($value),
            default => parent::get($name),
        };
    }


    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return '[' . implode(', ', $this->value) . ']';
    }
}
