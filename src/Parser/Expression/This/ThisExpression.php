<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\This;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class ThisExpression implements ExpressionInterface
{
    /**
     * ThisExpression constructor.
     *
     * @param TokenInterface $keyword
     */
    public function __construct(private TokenInterface $keyword)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitThisExpression($this);
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
}
