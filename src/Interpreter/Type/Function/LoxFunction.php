<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter\Type\Function;

use ExtendsSoftware\LoxPHP\Interpreter\Environment\EnvironmentInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Environment\Local\LocalEnvironment;
use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Interpreter\LoxCallableInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNil;
use ExtendsSoftware\LoxPHP\Interpreter\Type\LoxInstance;
use ExtendsSoftware\LoxPHP\Parser\Expression\Function\FunctionExpression;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use function count;
use function sprintf;

class LoxFunction implements LoxCallableInterface
{
    /**
     * LoxFunction constructor.
     *
     * @param FunctionExpression   $declaration
     * @param EnvironmentInterface $closure
     * @param bool                 $isInitializer
     * @param TokenInterface|null  $name
     */
    public function __construct(
        readonly private FunctionExpression   $declaration,
        readonly private EnvironmentInterface $closure,
        readonly private bool                 $isInitializer,
        readonly private ?TokenInterface      $name = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function call(InterpreterInterface $interpreter, array $arguments): mixed
    {
        $environment = new LocalEnvironment($this->closure);
        foreach ($this->declaration->getParameters() as $index => $parameter) {
            $environment = $environment->define($parameter->getLexeme(), $arguments[$index] ?? new LoxNil());
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
    public function arities(): array
    {
        return [count($this->declaration->getParameters())];
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        if ($this->name) {
            return sprintf('<function %s>', $this->name->getLexeme());
        }

        return '<fn>';
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
