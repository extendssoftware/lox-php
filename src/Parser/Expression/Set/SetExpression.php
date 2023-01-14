<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Set;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

class SetExpression implements ExpressionInterface
{
    /**
     * SetExpression constructor.
     *
     * @param ExpressionInterface $object
     * @param TokenInterface      $name
     * @param ExpressionInterface $value
     */
    public function __construct(
        private readonly ExpressionInterface $object,
        private readonly TokenInterface      $name,
        private readonly ExpressionInterface $value
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitSetExpression($this);
    }

    /**
     * Get object expression.
     *
     * @return ExpressionInterface
     */
    public function getObject(): ExpressionInterface
    {
        return $this->object;
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
