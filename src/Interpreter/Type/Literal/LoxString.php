<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

use function abs;
use function array_map;
use function explode;
use function preg_match;
use function preg_match_all;
use function str_replace;
use function strlen;
use function strrev;
use function trim;

class LoxString extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name, ?bool $nullSafe = null): mixed
    {
        return match ($name->getLexeme()) {
            'explode' => function ($separator = null): LoxArray {
                $separator = (string)$separator;
                if (strlen($separator) === 0) {
                    $separator = ' ';
                }

                return new LoxArray(explode($separator, $this->value));
            },
            'get' => function ($index = null): LoxNil|LoxString {
                $index = (int)(string)$index;
                if ($index < 0) {
                    $index = strlen($this->value) - abs($index);
                }

                if (isset($this->value[$index])) {
                    return new LoxString($this->value[$index]);
                }

                return new LoxNil();
            },
            'length' => function (): LoxNumber {
                return new LoxNumber(strlen($this->value));
            },
            'match' => function ($pattern): LoxArray {
                @preg_match((string)$pattern, $this->value, $matches);

                return new LoxArray(array_map(fn($match) => new LoxString($match), $matches ?: []));
            },
            'matchAll' => function ($pattern): LoxArray {
                @preg_match_all((string)$pattern, $this->value, $matches);

                return new LoxArray(array_map(fn($match) => new LoxString($match), $matches[0] ?? []));
            },
            'replace' => function ($search, $replace): LoxString {
                return new LoxString(str_replace((string)$search, (string)$replace, $this->value));
            },
            'reverse' => function (): LoxString {
                return new LoxString(strrev($this->value));
            },
            'trim' => function (): LoxString {
                return new LoxString(trim($this->value));
            },
            default => parent::get($name, $nullSafe),
        };
    }
}
