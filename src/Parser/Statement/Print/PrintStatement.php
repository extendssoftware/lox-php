<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\Print;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

class PrintStatement implements StatementInterface
{
    /**
     * PrintStatement constructor.
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
        return $visitor->visitPrintStatement($this);
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
