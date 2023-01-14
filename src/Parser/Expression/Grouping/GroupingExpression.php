<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Grouping;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

class GroupingExpression implements ExpressionInterface
{
    /**
     * GroupingExpression constructor.
     *
     * @param ExpressionInterface $expression
     */
    public function __construct(private readonly ExpressionInterface $expression)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitGroupingExpression($this);
    }

    /**
     * Get expression.
     *
     * @return ExpressionInterface
     */
    public function getExpression(): ExpressionInterface
    {
        return $this->expression;
    }
}
