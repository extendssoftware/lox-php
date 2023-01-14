<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Resolver;

use ArrayObject;
use ExtendsSoftware\LoxPHP\LoxExceptionInterface;
use ExtendsSoftware\LoxPHP\Parser\Expression\Assign\AssignExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Binary\BinaryExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Call\CallExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
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
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\Variable\VariableStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\While\WhileStatement;
use ExtendsSoftware\LoxPHP\Resolver\Error\CompileError;
use ExtendsSoftware\LoxPHP\Resolver\Type\ClassType;
use ExtendsSoftware\LoxPHP\Resolver\Type\FunctionType;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use SplDoublyLinkedList;

class Resolver implements ResolverInterface
{
    /**
     * Scopes.
     *
     * @var SplDoublyLinkedList<ArrayObject<string, boolean>>
     */
    private SplDoublyLinkedList $scopes;

    /**
     * Current function type.
     *
     * @var FunctionType
     */
    protected FunctionType $currentFunction = FunctionType::NONE;

    /**
     * Current class type.
     *
     * @var ClassType
     */
    private ClassType $currentClass = ClassType::NONE;

    /**
     * Resolver constructor.
     */
    public function __construct()
    {
        $this->scopes = new SplDoublyLinkedList();
        $this->scopes->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
        $this->scopes->push(new ArrayObject());
    }

    /**
     * @inheritDoc
     */
    public function resolve(ExpressionInterface|StatementInterface $statement): ResolverInterface
    {
        $statement->accept($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resolveAll(array $statements): ResolverInterface
    {
        foreach ($statements as $statement) {
            $this->resolve($statement);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function visitAssignExpression(AssignExpression $expression): mixed
    {
        $this->resolve($expression->getValue());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitBinaryExpression(BinaryExpression $expression): mixed
    {
        $this
            ->resolve($expression->getLeft())
            ->resolve($expression->getRight());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitCallExpression(CallExpression $expression): mixed
    {
        $this
            ->resolve($expression->getCallee())
            ->resolveAll($expression->getArguments());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitGetExpression(GetExpression $expression): mixed
    {
        $this->resolve($expression->getObject());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitGroupingExpression(GroupingExpression $expression): mixed
    {
        $this->resolve($expression->getExpression());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitLiteralExpression(LiteralExpression $expression): mixed
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitLogicalExpression(LogicalExpression $expression): mixed
    {
        $this
            ->resolve($expression->getLeft())
            ->resolve($expression->getRight());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitSetExpression(SetExpression $expression): mixed
    {
        $this
            ->resolve($expression->getValue())
            ->resolve($expression->getObject());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitSuperExpression(SuperExpression $expression): mixed
    {
        $name = $expression->getMethod();
        if ($this->currentClass === ClassType::NONE) {
            throw new CompileError("Can't use 'super' outside of a class.", $name->getLine(), $name->getColumn());
        } elseif ($this->currentClass !== ClassType::SUBCLASS) {
            throw new CompileError(
                "Can't use 'super' in a class with no superclass.",
                $name->getLine(),
                $name->getColumn()
            );
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitThisExpression(ThisExpression $expression): mixed
    {
        $keyword = $expression->getKeyword();
        if ($this->currentClass === ClassType::NONE) {
            throw new CompileError("Can't use 'this' outside of a class.", $keyword->getLine(), $keyword->getColumn());
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitUnaryExpression(UnaryExpression $expression): mixed
    {
        $this->resolve($expression->getRight());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitVariableExpression(VariableExpression $expression): mixed
    {
        $name = $expression->getName();
        if (!$this->scopes->isEmpty() &&
            $this->scopes->top()->offsetExists($name->getLexeme()) &&
            $this->scopes->top()->offsetGet($name->getLexeme()) === false) {
            throw new CompileError(
                "Can't read local variable in its own initializer",
                $name->getLine(),
                $name->getColumn()
            );
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitBlockStatement(BlockStatement $statement): mixed
    {
        $this->beginScope();
        $this->resolveAll($statement->getStatements());
        $this->endScope();

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitClassStatement(ClassStatement $statement): mixed
    {
        $enclosingClass = $this->currentClass;
        $this->currentClass = ClassType::INSTANCE;

        $this->declare($statement->getName());
        $this->define($statement->getName());

        $superclass = $statement->getSuperclass();
        if ($superclass) {
            $name = $superclass->getName();
            if ($statement->getName()->getLexeme() === $name->getLexeme()) {
                throw new CompileError("A class can't inherit from itself.", $name->getLine(), $name->getColumn());
            }

            $this->currentClass = ClassType::SUBCLASS;
            $this->resolve($superclass);

            $this->beginScope();
            $this->scopes->top()->offsetSet('super', true);
        }

        $this->beginScope();
        $this->scopes->top()->offsetSet('this', true);

        foreach ($statement->getMethods() as $method) {
            $type = FunctionType::METHOD;
            if ($method->getName()->getLexeme() === 'init') {
                $type = FunctionType::INITIALIZER;
            }

            $this->resolveFunction($method, $type);
        }

        $this->endScope();
        if ($superclass) {
            $this->endScope();
        }

        $this->currentClass = $enclosingClass;

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitExpressionStatement(ExpressionStatement $statement): mixed
    {
        $this->resolve($statement->getExpression());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitFunctionStatement(FunctionStatement $statement): mixed
    {
        // Declare and define function eagerly to allow function recursion.
        $this->declare($statement->getName());
        $this->define($statement->getName());

        $this->resolveFunction($statement, FunctionType::FUNCTION);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitIfStatement(IfStatement $statement): mixed
    {
        $this
            ->resolve($statement->getCondition())
            ->resolve($statement->getThen());

        $else = $statement->getElse();
        if ($else) {
            $this->resolve($else);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitPrintStatement(PrintStatement $statement): mixed
    {
        $this->resolve($statement->getExpression());

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitReturnStatement(ReturnStatement $statement): mixed
    {
        $name = $statement->getName();
        if ($this->currentFunction === FunctionType::NONE) {
            throw new CompileError("Can't return from top-level code.", $name->getLine(), $name->getColumn());
        }

        $value = $statement->getValue();
        if ($value) {
            if ($this->currentFunction === FunctionType::INITIALIZER) {
                throw new CompileError(
                    "Can't return a value from an initializer.",
                    $name->getLine(),
                    $name->getColumn()
                );
            }

            $this->resolve($value);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitVariableStatement(VariableStatement $statement): mixed
    {
        $name = $statement->getName();
        $this->declare($name);

        $initializer = $statement->getInitializer();
        if ($initializer) {
            $this->resolve($initializer);
        }

        $this->define($name);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function visitWhileStatement(WhileStatement $statement): mixed
    {
        $this
            ->resolve($statement->getCondition())
            ->resolve($statement->getBody());

        return null;
    }

    /**
     * Begin new scope.
     *
     * @return void
     */
    private function beginScope(): void
    {
        $this->scopes->push(new ArrayObject());
    }

    /**
     * End current scope.
     *
     * @return void
     */
    private function endScope(): void
    {
        $this->scopes->pop();
    }

    /**
     * Declare variable as not initialized.
     *
     * @param TokenInterface $name
     *
     * @return void
     * @throws CompileError
     */
    private function declare(TokenInterface $name): void
    {
        if (!$this->scopes->isEmpty()) {
            $scope = $this->scopes->top();
            if ($scope->offsetExists($name->getLexeme())) {
                throw new CompileError(
                    'Already a variable with this name in this scope.',
                    $name->getLine(),
                    $name->getColumn()
                );
            }

            $scope->offsetSet($name->getLexeme(), false);
        }
    }

    /**
     * Define variable as initialized.
     *
     * @param TokenInterface $name
     *
     * @return void
     */
    private function define(TokenInterface $name): void
    {
        if (!$this->scopes->isEmpty()) {
            $this->scopes->top()->offsetSet($name->getLexeme(), true);
        }
    }

    /**
     * Resolve function.
     *
     * @param FunctionStatement $statement
     * @param FunctionType      $type
     *
     * @return void
     * @throws LoxExceptionInterface
     */
    private function resolveFunction(FunctionStatement $statement, FunctionType $type): void
    {
        $enclosingFunction = $this->currentFunction;
        $this->currentFunction = $type;

        $this->beginScope();
        foreach ($statement->getParameters() as $parameter) {
            $this->declare($parameter);
            $this->define($parameter);
        }

        $this->resolveAll($statement->getBody());
        $this->endScope();

        $this->currentFunction = $enclosingFunction;
    }
}
