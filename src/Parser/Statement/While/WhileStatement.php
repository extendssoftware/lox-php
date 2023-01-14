<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\While;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

class WhileStatement implements StatementInterface
{
    /**
     * WhileStatement constructor.
     *
     * @param ExpressionInterface $condition
     * @param StatementInterface  $body
     */
    public function __construct(
        private readonly ExpressionInterface $condition,
        private readonly StatementInterface  $body
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitWhileStatement($this);
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
     * Get body statement.
     *
     * @return StatementInterface
     */
    public function getBody(): StatementInterface
    {
        return $this->body;
    }
}
