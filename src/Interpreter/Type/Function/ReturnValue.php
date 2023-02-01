<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Function;

use Exception;
use ExtendsSoftware\LoxPHP\LoxPHPExceptionInterface;

class ReturnValue extends Exception implements LoxPHPExceptionInterface
{
    /**
     * ReturnError constructor.
     *
     * @param mixed $value
     */
    public function __construct(readonly private mixed $value)
    {
        parent::__construct();
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
