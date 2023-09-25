<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\Expression;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

readonly class ExpressionStatement implements StatementInterface
{
    /**
     * ExpressionStatement constructor.
     *
     * @param ExpressionInterface $expression
     */
    public function __construct(private ExpressionInterface $expression)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitExpressionStatement($this);
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
