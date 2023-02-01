<?php

namespace ExtendsSoftware\LoxPHP\Interpreter\Type;

use Closure;
use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNil;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNumber;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use TypeError;
use function fopen;
use function fwrite;
use function implode;
use function is_resource;
use function microtime;
use function round;
use function sprintf;
use function time;

class LoxSystem extends LoxInstance
{
    /**
     * Output stream.
     *
     * @var resource
     */
    private $stream;

    /**
     * LoxSystem constructor.
     *
     * @param resource|null $stream
     */
    public function __construct($stream = null)
    {
        parent::__construct();

        $stream = $stream ?: fopen('php://stdout', 'w');
        if (!is_resource($stream)) {
            throw new TypeError(sprintf(
                'Stream must be of type resource, %s given.',
                gettype($stream)
            ));
        }

        $this->stream = $stream;
    }

    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name): Closure
    {
        return match ($name->getLexeme()) {
            'time' => function (InterpreterInterface $interpreter, $milliseconds = null): LoxNumber {
                if ($milliseconds && $interpreter->isTruthy($milliseconds)) {
                    $time = round(microtime(true) * 1000);
                } else {
                    $time = time();
                }

                return new LoxNumber($time);
            },
            'print' => function (...$messages): LoxNil {
                fwrite($this->stream, implode('', $messages) . PHP_EOL);

                return new LoxNil();
            },
            default => parent::get($name),
        };
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return 'System';
    }
}
