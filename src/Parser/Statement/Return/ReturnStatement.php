<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\Return;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class ReturnStatement implements StatementInterface
{
    /**
     * ReturnStatement constructor.
     *
     * @param TokenInterface $name
     * @param ExpressionInterface|null $value
     */
    public function __construct(
        private TokenInterface $name,
        private ?ExpressionInterface $value = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitReturnStatement($this);
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
     * Get return value.
     *
     * @return ExpressionInterface|null
     */
    public function getValue(): ?ExpressionInterface
    {
        return $this->value;
    }
}
