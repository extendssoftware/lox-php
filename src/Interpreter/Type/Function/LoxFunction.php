<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Function;

use ExtendsSoftware\LoxPHP\Interpreter\Environment\EnvironmentInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Environment\Local\LocalEnvironment;
use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\LoxCallableInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\LoxInstance;
use ExtendsSoftware\LoxPHP\Parser\Statement\Function\FunctionStatement;
use function count;
use function sprintf;

class LoxFunction implements LoxCallableInterface
{
    /**
     * LoxFunction constructor.
     *
     * @param FunctionStatement    $declaration
     * @param EnvironmentInterface $closure
     * @param bool                 $isInitializer
     */
    public function __construct(
        readonly private FunctionStatement    $declaration,
        readonly private EnvironmentInterface $closure,
        readonly private bool                 $isInitializer
    ) {
    }

    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed
    {
        $environment = new LocalEnvironment($this->closure);
        foreach ($this->declaration->getParameters() as $index => $parameter) {
            $environment = $environment->define($parameter->getLexeme(), $arguments[$index]);
        }

        try {
            $interpreter->executeBlock($this->declaration->getBody(), $environment);
        } catch (ReturnValue $return) {
            if ($this->isInitializer) {
                return $this->closure->get('this');
            }

            return $return->getValue();
        }


        if ($this->isInitializer) {
            return $this->closure->get('this');
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function arity(): int
    {
        return count($this->declaration->getParameters());
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return sprintf('<function %s>', $this->declaration->getName()->getLexeme());
    }

    /**
     * Bind LoxInstance.
     *
     * @param LoxInstance $instance
     *
     * @return LoxFunction
     */
    public function bind(LoxInstance $instance): LoxFunction
    {
        return new LoxFunction(
            $this->declaration,
            (new LocalEnvironment($this->closure))->define('this', $instance),
            $this->isInitializer
        );
    }
}
