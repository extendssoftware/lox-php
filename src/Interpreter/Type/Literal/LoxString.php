<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use InvalidArgumentException;
use function abs;
use function array_map;
use function array_merge;
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
    protected function getFunctions(): array
    {
        return array_merge(
            parent::getFunctions(),
            [
                'length' => fn(): LoxNumber => new LoxNumber(strlen($this->value)),
                'get' => function (string $index = '0'): LoxString|LoxNil {
                    $index = (int)$index;
                    if ($index < 0) {
                        $index = strlen($this->value) - abs($index);
                    }

                    if (isset($this->value[$index])) {
                        return new LoxString($this->value[$index]);
                    }

                    return new LoxNil();
                },
                'replace' => fn(string $search, string $replace): LoxString => new LoxString(
                    str_replace($search, $replace, $this->value)
                ),
                'explode' => function (string $separator = ' '): LoxArray {
                    if (strlen($separator) === 0) {
                        $separator = ' ';
                    }

                    return new LoxArray(explode($separator, $this->value));
                },
                'trim' => fn(): LoxString => new LoxString(trim($this->value)),
                'reverse' => fn(): LoxString => new LoxString(strrev($this->value)),
                'match' => fn(string $pattern): LoxBoolean => new LoxBoolean(@preg_match($pattern, $this->value) === 1),
                'matches' => function (string $pattern): LoxArray {
                    if (@preg_match_all($pattern, $this->value, $matches) === false) {
                        throw new InvalidArgumentException('Invalid regular expression.');
                    }

                    return new LoxArray(array_map(fn($match) => new LoxString($match), $matches[0]));
                },
            ]
        );
    }
}
