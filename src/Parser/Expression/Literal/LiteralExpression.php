<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression\Literal;

use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\Type\TokenType;

class LiteralExpression implements ExpressionInterface
{
    /**
     * LiteralExpression constructor.
     *
     * @param TokenType $type
     * @param mixed     $value
     */
    public function __construct(private readonly TokenType $type, private readonly mixed $value)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitLiteralExpression($this);
    }

    /**
     * Get token type.
     *
     * @return TokenType
     */
    public function getType(): TokenType
    {
        return $this->type;
    }

    /**
     * Get value.
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
