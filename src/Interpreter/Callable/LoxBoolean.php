<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Callable;

class LoxBoolean extends LoxLiteral
{
    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->value ? 'true' : 'false';
    }

    /**
     * @inheritDoc
     */
    protected function getFunctions(): array
    {
        return [];
    }
}
