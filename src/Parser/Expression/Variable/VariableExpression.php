<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Variable;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class VariableExpression implements ExpressionInterface
{
    /**
     * VariableExpression constructor.
     *
     * @param TokenInterface $name
     */
    public function __construct(private TokenInterface $name)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitVariableExpression($this);
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
}
