<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Interpreter;

use Closure;
use ExtendsSoftware\LoxPHP\Interpreter\Environment\EnvironmentInterface;
use ExtendsSoftware\LoxPHP\Interpreter\Environment\Global\GlobalEnvironment;
use ExtendsSoftware\LoxPHP\Interpreter\Environment\Local\LocalEnvironment;
use ExtendsSoftware\LoxPHP\Interpreter\Error\RuntimeError;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Class\LoxClass;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Function\LoxFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Function\ReturnValue;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LiteralFunction;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxArray;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxBoolean;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxLiteral;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNil;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxNumber;
use ExtendsSoftware\LoxPHP\Interpreter\Type\Literal\LoxString;
use ExtendsSoftware\LoxPHP\Interpreter\Type\LoxInstance;
use ExtendsSoftware\LoxPHP\Interpreter\Type\LoxSystem;
use ExtendsSoftware\LoxPHP\Parser\Expression\Array\ArrayExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Assign\AssignExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Binary\BinaryExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Call\CallExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Function\FunctionExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Get\GetExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Grouping\GroupingExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Literal\LiteralExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Logical\LogicalExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Set\SetExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Super\SuperExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\This\ThisExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Typeof\TypeofExpression;
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
use ReflectionClass;
use ReflectionException;
use Throwable;
use TypeError;
use function array_merge;
use function array_pop;
use function fopen;
use function fwrite;
use function implode;
use function in_array;
use function is_resource;
use function sprintf;
use function str_replace;

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

        $globals->define('system', new LoxSystem());

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
     */
    public function isTruthy(mixed $value): bool
    {
        if ($value instanceof LoxLiteral) {
            return (bool)$value->getValue();
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function visitArrayExpression(ArrayExpression $expression): LoxArray
    {
        $arguments = [];
        foreach ($expression->getArguments() as $argument) {
            $arguments[] = $argument->accept($this);
        }

        return new LoxArray($arguments);
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
    public function visitBinaryExpression(BinaryExpression $expression): LoxLiteral
    {
        $left = $expression->getLeft()->accept($this);
        $right = $expression->getRight()->accept($this);
        $operator = $expression->getOperator();

        switch ($operator->getType()) {
            case TokenType::GREATER:
                $this->checkNumberOperands($operator, $left, $right);

                return new LoxBoolean($left->getValue() > $right->getValue());
            case TokenType::GREATER_EQUAL:
                $this->checkNumberOperands($operator, $left, $right);

                return new LoxBoolean($left->getValue() >= $right->getValue());
            case TokenType::LESS:
                $this->checkNumberOperands($operator, $left, $right);

                return new LoxBoolean($left->getValue() < $right->getValue());
            case TokenType::LESS_EQUAL:
                $this->checkNumberOperands($operator, $left, $right);

                return new LoxBoolean($left->getValue() <= $right->getValue());
            case TokenType::BANG_EQUAL:
                return new LoxBoolean($left->getValue() != $right->getValue());
            case TokenType::EQUAL_EQUAL:
                return new LoxBoolean($left->getValue() == $right->getValue());
            case TokenType::MINUS:
                $this->checkNumberOperands($operator, $left, $right);

                return new LoxNumber($left->getValue() - $right->getValue());
            case TokenType::PLUS:
                if ($left instanceof LoxNumber && $right instanceof LoxNumber) {
                    return new LoxNumber($left->getValue() + $right->getValue());
                }

                if ($left instanceof LoxString && $right instanceof LoxString) {
                    return new LoxString($left->getValue() . $right->getValue());
                }

                if ($left instanceof LoxArray && $right instanceof LoxArray) {
                    return new LoxArray(array_merge($left->getValue(), $right->getValue()));
                }

                throw new RuntimeError(
                    'Operands must be two numbers, strings or arrays.',
                    $operator->getLine(),
                    $operator->getColumn()
                );
            case TokenType::SLASH:
                $this->checkNumberOperands($operator, $left, $right);

                if ($right->getValue() === 0.0) {
                    return new LoxNumber(0);
                }

                return new LoxNumber($left->getValue() / $right->getValue());
            case TokenType::STAR:
                $this->checkNumberOperands($operator, $left, $right);

                return new LoxNumber($left->getValue() * $right->getValue());
            case TokenType::MODULO:
                $this->checkNumberOperands($operator, $left, $right);

                return new LoxNumber(fmod($left->getValue(), $right->getValue()));
            default:
                return new LoxNil();
        }
    }

    /**
     * @inheritDoc
     * @throws RuntimeError|ReflectionException
     */
    public function visitCallExpression(CallExpression $expression): mixed
    {
        $token = $expression->getParen();

        $callee = $expression->getCallee()->accept($this);
        if ($callee instanceof Closure) {
            $callee = new LiteralFunction($callee);
        }

        if (!$callee instanceof LoxCallableInterface) {
            throw new RuntimeError('Can only call functions and classes.', $token->getLine(), $token->getColumn());
        }

        $arguments = [];
        foreach ($expression->getArguments() as $argument) {
            $arguments[] = $argument->accept($this);
        }

        $count = count($arguments);

        $arities = $callee->arities();
        if (!empty($arities) && !in_array($count, $arities)) {
            $expected = array_pop($arities);
            if (count($arities)) {
                $expected = implode(', ', $arities) . ' or ' . $expected;
            }

            throw new RuntimeError(
                sprintf('Expected %s arguments but got %d.', $expected, $count),
                $token->getLine(),
                $token->getColumn()
            );
        }

        try {
            return $callee->call($this, $arguments);
        } catch (Throwable $throwable) {
            throw new RuntimeError($throwable->getMessage(), $token->getLine(), $token->getColumn());
        }
    }

    /**
     * @inheritDoc
     */
    public function visitFunctionExpression(FunctionExpression $expression): LoxFunction
    {
        return new LoxFunction($expression, $this->environment, false);
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
    public function visitLiteralExpression(LiteralExpression $expression): LoxLiteral
    {
        $value = $expression->getValue();

        return match ($expression->getType()) {
            TokenType::TRUE, TokenType::FALSE => new LoxBoolean((bool)$value),
            TokenType::STRING => new LoxString((string)$value),
            TokenType::NUMBER => new LoxNumber((float)$value),
            default => new LoxNil(),
        };
    }

    /**
     * @inheritDoc
     */
    public function visitLogicalExpression(LogicalExpression $expression): mixed
    {
        $left = $expression->getLeft()->accept($this);
        if ($expression->getOperator()->getType() === TokenType::OR) {
            if ($this->isTruthy($left)) {
                return $left;
            }
        } else {
            if (!$this->isTruthy($left)) {
                return $left;
            }
        }

        return $this->isTruthy($expression->getRight()->accept($this));
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
     * @throws ReflectionException
     */
    public function visitTypeofExpression(TypeofExpression $expression): LoxString
    {
        $operand = $expression->getOperand()->accept($this);
        $reflection = new ReflectionClass($operand);

        // Remove leading 'Lox' from classname.
        return new LoxString(str_replace('Lox', '', $reflection->getShortName()));
    }

    /**
     * @inheritDoc
     */
    public function visitUnaryExpression(UnaryExpression $expression): LoxLiteral
    {
        $right = $expression->getRight()->accept($this);
        $operator = $expression->getOperator();

        switch ($operator->getType()) {
            case TokenType::BANG:
                return new LoxBoolean(!$this->isTruthy($right));
            case TokenType::MINUS:
                $this->checkNumberOperand($operator, $right);

                return new LoxNumber(-$right->getValue());
            default:
                return new LoxNil();
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
            $methods[(string)$methodLexeme] = new LoxFunction(
                $method->getFunction(),
                $this->environment,
                $methodLexeme === 'init',
                $name
            );
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
        $this->environment = $this->environment->define(
            $statement->getName()->getLexeme(),
            new LoxFunction($statement->getFunction(), $this->environment, false, $statement->getName())
        );

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitIfStatement(IfStatement $statement): mixed
    {
        if ($this->isTruthy($statement->getCondition()->accept($this))) {
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
        fwrite($this->stream, $statement->getExpression()->accept($this) . PHP_EOL);

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
        while ($this->isTruthy($statement->getCondition()->accept($this))) {
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
        if (!$operand instanceof LoxNumber) {
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
        if (!$left instanceof LoxNumber || !$right instanceof LoxNumber) {
            throw new RuntimeError('Operands must be numbers.', $operator->getLine(), $operator->getColumn());
        }
    }
}
