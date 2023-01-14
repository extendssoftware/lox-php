<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Resolver;

use ExtendsSoftware\LoxPHP\LoxExceptionInterface;
use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

interface ResolverInterface extends VisitorInterface
{
    /**
     * Resolve statement.
     *
     * @param ExpressionInterface|StatementInterface $statement
     *
     * @return ResolverInterface
     * @throws LoxExceptionInterface
     */
    public function resolve(ExpressionInterface|StatementInterface $statement): ResolverInterface;

    /**
     * Resolve statements.
     *
     * @param array<ExpressionInterface|StatementInterface> $statements
     *
     * @return ResolverInterface
     * @throws LoxExceptionInterface
     */
    public function resolveAll(array $statements): ResolverInterface;
}
