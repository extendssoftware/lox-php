<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Statement;

use ExtendsSoftware\LoxPHP\LoxPHPExceptionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

interface StatementInterface
{
    /**
     * Accept visitor.
     *
     * @param VisitorInterface $visitor
     *
     * @return bool
     * @throws LoxPHPExceptionInterface
     */
    public function accept(VisitorInterface $visitor): mixed;
}
