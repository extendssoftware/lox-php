<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\Variable;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

class VariableStatement implements StatementInterface
{
    /**
     * VariableStatement constructor.
     *
     * @param TokenInterface           $name
     * @param ExpressionInterface|null $initializer
     */
    public function __construct(
        private readonly TokenInterface       $name,
        private readonly ?ExpressionInterface $initializer = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitVariableStatement($this);
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
     * Get initializer expression.
     *
     * @return ExpressionInterface|null
     */
    public function getInitializer(): ?ExpressionInterface
    {
        return $this->initializer;
    }
}
