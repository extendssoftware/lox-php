<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Unary;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class UnaryExpression implements ExpressionInterface
{
    /**
     * UnaryExpression constructor.
     *
     * @param TokenInterface $operator
     * @param ExpressionInterface $right
     */
    public function __construct(private TokenInterface $operator, private ExpressionInterface $right)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitUnaryExpression($this);
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
