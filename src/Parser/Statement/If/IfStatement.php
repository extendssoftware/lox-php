<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\If;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

readonly class IfStatement implements StatementInterface
{
    /**
     * IfStatement constructor.
     *
     * @param ExpressionInterface $condition
     * @param StatementInterface $then
     * @param StatementInterface|null $else
     */
    public function __construct(
        private ExpressionInterface $condition,
        private StatementInterface $then,
        private ?StatementInterface $else = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitIfStatement($this);
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
     * Get then statement.
     *
     * @return StatementInterface
     */
    public function getThen(): StatementInterface
    {
        return $this->then;
    }

    /**
     * Get else statement.
     *
     * @return StatementInterface|null
     */
    public function getElse(): ?StatementInterface
    {
        return $this->else;
    }
}
