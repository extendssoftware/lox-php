<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\Class;

use ExtendsSoftware\LoxPHP\Parser\Expression\Variable\VariableExpression;
use ExtendsSoftware\LoxPHP\Parser\Statement\Function\FunctionStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

class ClassStatement implements StatementInterface
{
    /**
     * ClassStatement constructor.
     *
     * @param TokenInterface            $name
     * @param VariableExpression|null   $superclass
     * @param array<FunctionStatement> $methods
     */
    public function __construct(
        readonly private TokenInterface      $name,
        readonly private ?VariableExpression $superclass,
        readonly private array               $methods
    ) {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitClassStatement($this);
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
     * Get superclass expression.
     *
     * @return VariableExpression|null
     */
    public function getSuperclass(): ?VariableExpression
    {
        return $this->superclass;
    }

    /**
     * Get methods.
     *
     * @return array<FunctionStatement>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }
}
