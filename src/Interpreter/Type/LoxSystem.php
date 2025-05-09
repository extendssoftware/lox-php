<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type;

use Closure;
use DateTime;
use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNil;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNumber;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxString;
use ExtendsSoftware\LoxPHP\LoxPHPInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use TypeError;

use function fopen;
use function fwrite;
use function gettype;
use function implode;
use function is_resource;
use function sprintf;

use const PHP_EOL;

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
            throw new TypeError(
                sprintf(
                    'Stream must be of type resource, %s given.',
                    gettype($stream)
                )
            );
        }

        $this->stream = $stream;
    }

    /**
     * @inheritDoc
     */
    public function get(TokenInterface $name, ?bool $nullSafe = null): Closure
    {
        return match ($name->getLexeme()) {
            'version' => function (): LoxString {
                return new LoxString(LoxPHPInterface::VERSION);
            },
            'time' => function (InterpreterInterface $interpreter, $milliseconds = null): LoxNumber {
                $format = 'U';
                if ($milliseconds && $interpreter->isTruthy($milliseconds)) {
                    $format .= 'v';
                }

                return new LoxNumber((new DateTime())->format($format));
            },
            'print' => function (...$messages): LoxNil {
                fwrite($this->stream, implode('', $messages) . PHP_EOL);

                return new LoxNil();
            },
            'log' => function (...$messages): LoxNil {
                fwrite(
                    $this->stream,
                    (new DateTime())->format('Y-m-d H:i:s.v: ') . implode('', $messages) . PHP_EOL
                );

                return new LoxNil();
            },
            default => parent::get($name, $nullSafe),
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
