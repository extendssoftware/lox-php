<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Logical;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class LogicalExpression implements ExpressionInterface
{
    /**
     * LogicalExpression constructor.
     *
     * @param ExpressionInterface $left
     * @param TokenInterface $operator
     * @param ExpressionInterface $right
     */
    public function __construct(
        private ExpressionInterface $left,
        private TokenInterface $operator,
        private ExpressionInterface $right
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitLogicalExpression($this);
    }

    /**
     * Get left expression.
     *
     * @return ExpressionInterface
     */
    public function getLeft(): ExpressionInterface
    {
        return $this->left;
    }

    /**
     * Get operator token.
     *
     * @return TokenInterface
     */
    public function getOperator(): TokenInterface
    {
        return $this->operator;
    }

    /**
     * Get right expression.
     *
     * @return ExpressionInterface
     */
    public function getRight(): ExpressionInterface
    {
        return $this->right;
    }
}
