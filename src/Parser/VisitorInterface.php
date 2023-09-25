<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser;

use ExtendsSoftware\LoxPHP\LoxPHPExceptionInterface;
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
use ExtendsSoftware\LoxPHP\Parser\Statement\Return\ReturnStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\Variable\VariableStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\While\WhileStatement;

interface VisitorInterface
{
    /**
     * Visit array expression.
     *
     * @param ArrayExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitArrayExpression(ArrayExpression $expression): mixed;

    /**
     * Visit assign expression.
     *
     * @param AssignExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitAssignExpression(AssignExpression $expression): mixed;

    /**
     * Visit binary expression.
     *
     * @param BinaryExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitBinaryExpression(BinaryExpression $expression): mixed;

    /**
     * Visit call expression.
     *
     * @param CallExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitCallExpression(CallExpression $expression): mixed;

    /**
     * Visit function expression.
     *
     * @param FunctionExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitFunctionExpression(FunctionExpression $expression): mixed;

    /**
     * Visit get expression.
     *
     * @param GetExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitGetExpression(GetExpression $expression): mixed;

    /**
     * Visit grouping expression.
     *
     * @param GroupingExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitGroupingExpression(GroupingExpression $expression): mixed;

    /**
     * Visit literal expression.
     *
     * @param LiteralExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitLiteralExpression(LiteralExpression $expression): mixed;

    /**
     * Visit logical expression.
     *
     * @param LogicalExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitLogicalExpression(LogicalExpression $expression): mixed;

    /**
     * Visit set expression.
     *
     * @param SetExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitSetExpression(SetExpression $expression): mixed;

    /**
     * Visit super expression.
     *
     * @param SuperExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitSuperExpression(SuperExpression $expression): mixed;

    /**
     * Visit this expression.
     *
     * @param ThisExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitThisExpression(ThisExpression $expression): mixed;

    /**
     * Visit typeof expression.
     *
     * @param TypeofExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitTypeofExpression(TypeofExpression $expression): mixed;

    /**
     * Visit unary expression.
     *
     * @param UnaryExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitUnaryExpression(UnaryExpression $expression): mixed;

    /**
     * Visit variable expression.
     *
     * @param VariableExpression $expression
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitVariableExpression(VariableExpression $expression): mixed;

    /**
     * Visit block statement.
     *
     * @param BlockStatement $statement
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitBlockStatement(BlockStatement $statement): mixed;

    /**
     * Visit class statement.
     *
     * @param ClassStatement $statement
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitClassStatement(ClassStatement $statement): mixed;

    /**
     * Visit expression statement.
     *
     * @param ExpressionStatement $statement
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitExpressionStatement(ExpressionStatement $statement): mixed;

    /**
     * Visit function statement.
     *
     * @param FunctionStatement $statement
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitFunctionStatement(FunctionStatement $statement): mixed;

    /**
     * Visit if statement.
     *
     * @param IfStatement $statement
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitIfStatement(IfStatement $statement): mixed;

    /**
     * Visit return statement.
     *
     * @param ReturnStatement $statement
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitReturnStatement(ReturnStatement $statement): mixed;

    /**
     * Visit variable statement.
     *
     * @param VariableStatement $statement
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitVariableStatement(VariableStatement $statement): mixed;

    /**
     * Visit while statement.
     *
     * @param WhileStatement $statement
     *
     * @return mixed
     * @throws LoxPHPExceptionInterface
     */
    public function visitWhileStatement(WhileStatement $statement): mixed;
}
