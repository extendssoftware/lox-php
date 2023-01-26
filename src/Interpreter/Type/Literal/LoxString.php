<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String\Explode;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String\Get;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String\Length;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String\PregMatch;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String\PregMatchAll;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String\Replace;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String\Reverse;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\Function\String\Trim;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

class LoxString extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name): mixed
    {
        $value = $this->value;
        return match ($name->getLexeme()) {
            'explode' => new Explode($value),
            'get' => new Get($value),
            'length' => new Length($value),
            'match' => new PregMatch($value),
            'matchAll' => new PregMatchAll($value),
            'replace' => new Replace($value),
            'reverse' => new Reverse($value),
            'trim' => new Trim($value),
            default => parent::get($name),
        };
    }
}
