<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Resolver\Type;

enum ClassType
{
    case NONE;
    case INSTANCE;
    case SUBCLASS;
}
