<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Literal;

use function array_merge;
use function str_replace;
use function strlen;

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
                'at' => function ($index = 0) {
                    $index = (int)(string)$index;
                    if ($index < 0) {
                        $index = strlen($this->value) - abs($index);
                    }

                    if (isset($this->value[$index])) {
                        return new LoxString($this->value[$index]);
                    }

                    return new LoxNil();
                },
                'replace' => fn($search, $replace) => new LoxString(
                    str_replace((string)$search, (string)$replace, $this->value)
                ),
            ]
        );
    }
}
