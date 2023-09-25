<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Super;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class SuperExpression implements ExpressionInterface
{
    /**
     * SuperExpression constructor.
     *
     * @param TokenInterface $keyword
     * @param TokenInterface $method
     */
    public function __construct(private TokenInterface $keyword, private TokenInterface $method)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitSuperExpression($this);
    }

    /**
     * Get keyword token.
     *
     * @return TokenInterface
     */
    public function getKeyword(): TokenInterface
    {
        return $this->keyword;
    }

    /**
     * Get method token.
     *
     * @return TokenInterface
     */
    public function getMethod(): TokenInterface
    {
        return $this->method;
    }
}
