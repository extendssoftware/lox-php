<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter;

use ExtendsSoftware\LoxPHP\Interpreter\Callable\LoxCallableInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Callable\LoxClass;
use ExtendsSoftware\LoxPHP\Interpreter\Callable\LoxFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Callable\LoxInstance;
use ExtendsSoftware\LoxPHP\Interpreter\Environment\EnvironmentInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Environment\Global\GlobalEnvironment;
use ExtendsSoftware\LoxPHP\Interpreter\Environment\Local\LocalEnvironment;
use ExtendsSoftware\LoxPHP\Interpreter\Error\RuntimeError;
use ExtendsSoftware\LoxPHP\Parser\Expression\Assign\AssignExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Binary\BinaryExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Call\CallExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Get\GetExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Grouping\GroupingExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Literal\LiteralExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Logical\LogicalExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Set\SetExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Super\SuperExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\This\ThisExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Unary\UnaryExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Variable\VariableExpression;
use ExtendsSoftware\LoxPHP\Parser\Statement\Block\BlockStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\Class\ClassStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\Expression\ExpressionStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\Function\FunctionStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\If\IfStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\Print\PrintStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\Return\ReturnStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\Variable\VariableStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\While\WhileStatement;
use ExtendsSoftware\LoxPHP\Parser\VisitorInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\Type\TokenType;
use TypeError;
use function count;
use function fwrite;
use function gettype;
use function is_float;
use function is_resource;
use function sprintf;

class Interpreter implements InterpreterInterface, VisitorInterface
{
    /**
     * Current environment.
     *
     * @var EnvironmentInterface
     */
    private EnvironmentInterface $environment;

    /**
     * Output stream.
     *
     * @var resource
     */
    private $stream;

    /**
     * Interpreter constructor.
     *
     * @param EnvironmentInterface $globals
     * @param resource             $stream
     */
    public function __construct(
        private readonly EnvironmentInterface $globals = new GlobalEnvironment(),
        mixed                                 $stream = null
    ) {
        $stream = $stream ?: fopen('php://stdout', 'w');
        if (!is_resource($stream)) {
            throw new TypeError(sprintf(
                'Stream must be of type resource, %s given.',
                gettype($stream)
            ));
        }

        $this->stream = $stream;
        $this->environment = $this->globals;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $statements): InterpreterInterface
    {
        foreach ($statements as $statement) {
            $statement->accept($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function executeBlock(array $statements, EnvironmentInterface $environment): InterpreterInterface
    {
        $previous = $this->environment;
        try {
            $this->environment = $environment;
            foreach ($statements as $statement) {
                $statement->accept($this);
            }
        } finally {
            $this->environment = $previous;
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws RuntimeError
     */
    public function visitAssignExpression(AssignExpression $expression): mixed
    {
        $value = $expression->getValue()->accept($this);
        $this->environment->assign($expression->getName(), $value);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function visitBinaryExpression(BinaryExpression $expression): mixed
    {
        $left = $expression->getLeft()->accept($this);
        $right = $expression->getRight()->accept($this);
        $operator = $expression->getOperator();

        switch ($operator->getType()) {
            case TokenType::GREATER:
                $this->checkNumberOperands($operator, $left, $right);

                return $left > $right;
            case TokenType::GREATER_EQUAL:
                $this->checkNumberOperands($operator, $left, $right);

                return $left >= $right;
            case TokenType::LESS:
                $this->checkNumberOperands($operator, $left, $right);

                return $left < $right;
            case TokenType::LESS_EQUAL:
                $this->checkNumberOperands($operator, $left, $right);

                return $left <= $right;
            case TokenType::BANG_EQUAL:
                return $left != $right;
            case TokenType::EQUAL_EQUAL:
                return $left == $right;
            case TokenType::MINUS:
                $this->checkNumberOperands($operator, $left, $right);

                return $left - $right;
            case TokenType::PLUS:
                if (is_float($left) && is_float($right)) {
                    return $left + $right;
                }

                if (is_string($left) && is_string($right)) {
                    return $left . $right;
                }

                throw new RuntimeError(
                    'Operands must be two numbers or two strings.',
                    $operator->getLine(),
                    $operator->getColumn()
                );
            case TokenType::SLASH:
                $this->checkNumberOperands($operator, $left, $right);

                if ($right === 0.0) {
                    throw new RuntimeError("Can't divide by zero.", $operator->getLine(), $operator->getColumn());
                }

                return $left / $right;
            case TokenType::STAR:
                $this->checkNumberOperands($operator, $left, $right);

                return $left * $right;
            default:
                return null;
        }
    }

    /**
     * @inheritDoc
     * @throws RuntimeError
     */
    public function visitCallExpression(CallExpression $expression): mixed
    {
        $callee = $expression->getCallee()->accept($this);
        if (!$callee instanceof LoxCallableInterface) {
            $token = $expression->getParen();

            throw new RuntimeError('Can only call functions and classes.', $token->getLine(), $token->getColumn());
        }

        $arguments = [];
        foreach ($expression->getArguments() as $argument) {
            $arguments[] = $argument->accept($this);
        }

        $count = count($arguments);
        if ($count !== $callee->arity()) {
            $token = $expression->getParen();

            throw new RuntimeError(
                sprintf('Expected %d arguments but got %d.', $callee->arity(), $count),
                $token->getLine(),
                $token->getColumn()
            );
        }

        return $callee->call($this, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function visitGetExpression(GetExpression $expression): mixed
    {
        $name = $expression->getName();
        $object = $expression->getObject()->accept($this);
        if ($object instanceof LoxInstance) {
            return $object->get($name);
        }

        throw new RuntimeError('Only instances have properties.', $name->getLine(), $name->getColumn());
    }

    /**
     * @inheritDoc
     */
    public function visitGroupingExpression(GroupingExpression $expression): mixed
    {
        return $expression->getExpression()->accept($this);
    }

    /**
     * @inheritDoc
     */
    public function visitLiteralExpression(LiteralExpression $expression): mixed
    {
        $value = $expression->getValue();

        return match ($expression->getType()) {
            TokenType::STRING => (string)$value,
            TokenType::NUMBER => (float)$value,
            default => $value,
        };
    }

    /**
     * @inheritDoc
     */
    public function visitLogicalExpression(LogicalExpression $expression): mixed
    {
        $left = $expression->getLeft()->accept($this);
        if ($expression->getOperator()->getType() === TokenType::OR) {
            if ($left) {
                return $left;
            }
        } else {
            if (!$left) {
                return $left;
            }
        }

        return $expression->getRight()->accept($this);
    }

    /**
     * @inheritDoc
     */
    public function visitSetExpression(SetExpression $expression): mixed
    {
        $name = $expression->getName();
        $object = $expression->getObject()->accept($this);
        if (!$object instanceof LoxInstance) {
            throw new RuntimeError("Only instances have fields.", $name->getLine(), $name->getColumn());
        }

        $value = $expression->getValue()->accept($this);
        $object->set($name, $value);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function visitSuperExpression(SuperExpression $expression): mixed
    {
        $superclass = $this->environment->get('super');
        $object = $this->environment->get('this');

        $method = $superclass->findMethod($expression->getMethod()->getLexeme());
        if (!$method) {
            $name = $expression->getMethod();

            throw new RuntimeError(
                sprintf("Undefined property '%s'.", $name->getLexeme()),
                $name->getLine(),
                $name->getColumn()
            );
        }

        return $method->bind($object);
    }

    /**
     * @inheritDoc
     */
    public function visitThisExpression(ThisExpression $expression): mixed
    {
        return $this->environment->get($expression->getKeyword()->getLexeme());
    }

    /**
     * @inheritDoc
     */
    public function visitUnaryExpression(UnaryExpression $expression): int|bool|null|float
    {
        $right = $expression->getRight()->accept($this);
        $operator = $expression->getOperator();

        switch ($operator->getType()) {
            case TokenType::BANG:
                return !$right;
            case TokenType::MINUS:
                $this->checkNumberOperand($operator, $right);

                return -$right;
            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     * @throws RuntimeError
     */
    public function visitVariableExpression(VariableExpression $expression): mixed
    {
        return $this->environment->get($expression->getName()->getLexeme());
    }

    /**
     * @inheritDoc
     */
    public function visitBlockStatement(BlockStatement $statement): mixed
    {
        $this->executeBlock($statement->getStatements(), new LocalEnvironment($this->environment));

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitClassStatement(ClassStatement $statement): mixed
    {
        $name = $statement->getName();

        $superclass = null;
        if ($statement->getSuperclass()) {
            $superclass = $statement->getSuperclass()->accept($this);
            if (!$superclass instanceof LoxClass) {
                throw new RuntimeError('Superclass must be a class.', $name->getLine(), $name->getColumn());
            }
        }

        $classLexeme = $statement->getName()->getLexeme();
        $this->environment = $this->environment->define($classLexeme, null);

        if ($superclass) {
            $this->environment = new LocalEnvironment($this->environment);
            $this->environment = $this->environment->define('super', $superclass);
        }

        $methods = [];
        foreach ($statement->getMethods() as $method) {
            $methodLexeme = $method->getName()->getLexeme();
            $methods[(string)$methodLexeme] = new LoxFunction($method, $this->environment, $methodLexeme === 'init');
        }

        $class = new LoxClass($classLexeme, $superclass, $methods);

        if ($superclass) {
            $enclosing = $this->environment->getEnclosing();
            if ($enclosing) {
                $this->environment = $enclosing;
            }
        }

        $this->environment->assign($statement->getName(), $class);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitExpressionStatement(ExpressionStatement $statement): mixed
    {
        $statement->getExpression()->accept($this);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitFunctionStatement(FunctionStatement $statement): mixed
    {
        $function = new LoxFunction($statement, $this->environment, false);
        $this->environment = $this->environment->define($statement->getName()->getLexeme(), $function);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitIfStatement(IfStatement $statement): mixed
    {
        if ($statement->getCondition()->accept($this)) {
            $statement->getThen()->accept($this);
        } elseif ($statement->getElse()) {
            $statement->getElse()->accept($this);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitPrintStatement(PrintStatement $statement): mixed
    {
        fwrite($this->stream, $this->stringify($statement->getExpression()->accept($this)) . PHP_EOL);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitReturnStatement(ReturnStatement $statement): mixed
    {
        throw new ReturnValue(
            $statement->getValue()?->accept($this)
        );
    }

    /**
     * @inheritDoc
     */
    public function visitVariableStatement(VariableStatement $statement): mixed
    {
        $this->environment = $this->environment->define(
            $statement->getName()->getLexeme(),
            $statement->getInitializer()?->accept($this)
        );

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitWhileStatement(WhileStatement $statement): mixed
    {
        while ($statement->getCondition()->accept($this)) {
            $statement->getBody()->accept($this);
        }

        return null;
    }

    /**
     * Check if right hand side is a number.
     *
     * @param TokenInterface $operator
     * @param mixed          $operand
     *
     * @return void
     * @throws RuntimeError
     */
    private function checkNumberOperand(TokenInterface $operator, mixed $operand): void
    {
        if (!is_float($operand)) {
            throw new RuntimeError('Operand must be a number.', $operator->getLine(), $operator->getColumn());
        }
    }

    /**
     * Check if right hand side is a number.
     *
     * @param TokenInterface $operator
     * @param mixed          $left
     * @param mixed          $right
     *
     * @return void
     * @throws RuntimeError
     */
    private function checkNumberOperands(TokenInterface $operator, mixed $left, mixed $right): void
    {
        if (!is_float($left) || !is_float($right)) {
            throw new RuntimeError('Operands must be numbers.', $operator->getLine(), $operator->getColumn());
        }
    }

    /**
     * Stringify value.
     *
     * @param mixed $value
     *
     * @return string
     */
    private function stringify(mixed $value): string
    {
        return match ($value) {
            null => 'nil',
            true => 'true',
            false => 'false',
            default => (string)$value,
        };
    }
}
