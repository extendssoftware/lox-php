<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Call;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class CallExpression implements ExpressionInterface
{
    /**
     * CallExpression constructor.
     *
     * @param ExpressionInterface $callee
     * @param TokenInterface $paren
     * @param array<ExpressionInterface> $arguments
     */
    public function __construct(
        private ExpressionInterface $callee,
        private TokenInterface $paren,
        private array $arguments
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitCallExpression($this);
    }

    /**
     * Get left expression.
     *
     * @return ExpressionInterface
     */
    public function getCallee(): ExpressionInterface
    {
        return $this->callee;
    }

    /**
     * Get operator token.
     *
     * @return TokenInterface
     */
    public function getParen(): TokenInterface
    {
        return $this->paren;
    }

    /**
     * Get expression arguments.
     *
     * @return array<ExpressionInterface>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
