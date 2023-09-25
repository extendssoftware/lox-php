<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Function;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class FunctionExpression implements ExpressionInterface
{
    /**
     * FunctionExpression constructor.
     *
     * @param array<TokenInterface> $parameters
     * @param array<StatementInterface> $body
     */
    public function __construct(private array $parameters, private array $body)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitFunctionExpression($this);
    }

    /**
     * Get parameters.
     *
     * @return array<TokenInterface>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get body.
     *
     * @return array<StatementInterface>
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
