<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement;

use ExtendsSoftware\LoxPHP\LoxExceptionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

interface StatementInterface
{
    /**
     * Accept visitor.
     *
     * @param VisitorInterface $visitor
     *
     * @return bool
     * @throws LoxExceptionInterface
     */
    public function accept(VisitorInterface $visitor): mixed;
}
