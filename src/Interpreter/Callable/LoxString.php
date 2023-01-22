<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Callable;

use function strlen;

class LoxString extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    protected function getFunctions(): array
    {
        return [
            'length' => function () {
                return new LoxNumber(strlen($this->value));
            },
        ];
    }
}
