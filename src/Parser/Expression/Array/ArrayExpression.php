<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Array;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

class ArrayExpression implements ExpressionInterface
{
    /**
     * ArrayExpression constructor.
     *
     * @param array<int, ExpressionInterface> $arguments
     */
    public function __construct(readonly private array $arguments)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitArrayExpression($this);
    }

    /**
     * Get arguments.
     *
     * @return array<int, ExpressionInterface>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
