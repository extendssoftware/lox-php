<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Callable;

use function array_merge;
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
            ]
        );
    }
}
