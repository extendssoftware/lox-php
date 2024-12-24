<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Resolver\Type;

enum FunctionType
{
    case NONE;
    case FUNCTION;
    case METHOD;
    case INITIALIZER;
}
