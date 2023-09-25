<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser\Expression;

use ExtendsSoftware\LoxPHP\LoxPHPExceptionInterface;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;

interface ExpressionInterface
{
    /**
     * Accept visitor.
     *
     * @param VisitorInterface $visitor
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function accept(VisitorInterface $visitor): mixed;
}
