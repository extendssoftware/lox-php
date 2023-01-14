<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression;

use ExtendsSoftware\LoxPHP\LoxExceptionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

interface ExpressionInterface
{
    /**
     * Accept visitor.
     *
     * @param VisitorInterface $visitor
     *
     * @return mixed
     * @throws LoxExceptionInterface
     */
    public function accept(VisitorInterface $visitor): mixed;
}
