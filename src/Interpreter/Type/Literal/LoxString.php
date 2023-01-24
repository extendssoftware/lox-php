<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use function array_merge;
use function explode;
use function str_replace;
use function strlen;
use function strrev;

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
                'length' => fn() => new LoxNumber(strlen($this->value)),
                'get' => function (string $index = '0') {
                    $index = (int)$index;
                    if ($index < 0) {
                        $index = strlen($this->value) - abs($index);
                    }

                    if (isset($this->value[$index])) {
                        return new LoxString($this->value[$index]);
                    }

                    return new LoxNil();
                },
                'replace' => fn(string $search, string $replace) => new LoxString(
                    str_replace($search, $replace, $this->value)
                ),
                'explode' => function (string $separator = ' ') {
                    if (strlen($separator) === 0) {
                        $separator = ' ';
                    }

                    return new LoxArray(explode($separator, $this->value));
                },
                'trim' => fn() => new LoxString(trim($this->value)),
                'reverse' => fn() => new LoxString(strrev($this->value)),
            ]
        );
    }
}
