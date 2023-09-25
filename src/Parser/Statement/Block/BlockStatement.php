<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement\Block;

use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

readonly class BlockStatement implements StatementInterface
{
    /**
     * BlockStatement constructor.
     *
     * @param array<StatementInterface> $statements
     */
    public function __construct(private array $statements)
    {
    }

    /**
     * @inheritDoc
     */
    public function accept(VisitorInterface $visitor): mixed
    {
        return $visitor->visitBlockStatement($this);
    }

    /**
     * Get statements.
     *
     * @return array<StatementInterface>
     */
    public function getStatements(): array
    {
        return $this->statements;
    }
}
