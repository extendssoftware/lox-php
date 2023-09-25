<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Assign;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class AssignExpression implements ExpressionInterface
{
    /**
     * AssignExpression constructor.
     *
     * @param TokenInterface $name
     * @param ExpressionInterface $value
     */
    public function __construct(
        private TokenInterface $name,
        private ExpressionInterface $value
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitAssignExpression($this);
    }

    /**
     * Get name token.
     *
     * @return TokenInterface
     */
    public function getName(): TokenInterface
    {
        return $this->name;
    }

    /**
     * Get value expression.
     *
     * @return ExpressionInterface
     */
    public function getValue(): ExpressionInterface
    {
        return $this->value;
    }
}
