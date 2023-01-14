<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\Function;

use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;

class FunctionStatement implements StatementInterface
{
    /**
     * FunctionStatement constructor.
     *
     * @param TokenInterface            $name
     * @param array<TokenInterface>     $parameters
     * @param array<StatementInterface> $body
     */
    public function __construct(
        readonly private TokenInterface $name,
        readonly private array          $parameters,
        readonly private array          $body
    ) {
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
