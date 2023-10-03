<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Get;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

readonly class GetExpression implements ExpressionInterface
{
    /**
     * GetExpression constructor.
     *
     * @param ExpressionInterface $object
     * @param TokenInterface $name
     * @param bool|null $nullSafe
     */
    public function __construct(
        private ExpressionInterface $object,
        private TokenInterface $name,
        private ?bool $nullSafe = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitGetExpression($this);
    }

    /**
     * Get object
     *
     * @return ExpressionInterface
     */
    public function getObject(): ExpressionInterface
    {
        return $this->object;
    }

    /**
     * Get name.
     *
     * @return TokenInterface
     */
    public function getName(): TokenInterface
    {
        return $this->name;
    }

    /**
     * Get null safe boolean.
     *
     * @return bool|null
     */
    public function getNullSafe(): ?bool
    {
        return $this->nullSafe;
    }
}
