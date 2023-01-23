<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Typeof;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

class TypeofExpression implements ExpressionInterface
{
    /**
     * TypeofExpression constructor.
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
        return $visitor->visitTypeofExpression($this);
    }

    /**
     * Get operand expression.
     *
     * @return ExpressionInterface
     */
    public function getOperand(): ExpressionInterface
    {
        return $this->expression;
    }
}
