<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\Function;

use ExtendsSoftware\LoxPHP\Parser\Expression\Function\FunctionExpression;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

class FunctionStatement implements StatementInterface
{
    /**
     * FunctionStatement constructor.
     *
     * @param TokenInterface     $name
     * @param FunctionExpression $function
     */
    public function __construct(readonly private TokenInterface $name, readonly private FunctionExpression $function)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitFunctionStatement($this);
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
     * Get function expression.
     *
     * @return FunctionExpression
     */
    public function getFunction(): FunctionExpression
    {
        return $this->function;
    }
}
