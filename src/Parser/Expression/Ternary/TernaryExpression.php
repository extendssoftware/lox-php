<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Ternary;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

readonly class TernaryExpression implements ExpressionInterface
{
    /**
     * TernaryExpression constructor.
     *
     * @param ExpressionInterface $condition
     * @param ExpressionInterface|null $then
     * @param ExpressionInterface $else
     */
    public function __construct(
        private ExpressionInterface $condition,
        private ?ExpressionInterface $then,
        private ExpressionInterface $else
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitTernaryExpression($this);
    }

    /**
     * Get condition expression.
     *
     * @return ExpressionInterface
     */
    public function getCondition(): ExpressionInterface
    {
        return $this->condition;
    }

    /**
     * Get then expression.
     *
     * @return ExpressionInterface|null
     */
    public function getThen(): ?ExpressionInterface
    {
        return $this->then;
    }

    /**
     * Get else expression.
     *
     * @return ExpressionInterface
     */
    public function getElse(): ExpressionInterface
    {
        return $this->else;
    }
}
