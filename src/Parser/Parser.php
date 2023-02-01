<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Parser;

use ExtendsSoftware\LoxPHP\Parser\Error\ParseError;
use ExtendsSoftware\LoxPHP\Parser\Expression\Array\ArrayExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Assign\AssignExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Binary\BinaryExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\Call\CallExpression;
use ExtendsSoftware\LoxPHP\Parser\Expression\ExpressionInterface;
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
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\Variable\VariableStatement;
use ExtendsSoftware\LoxPHP\Parser\Statement\While\WhileStatement;
use ExtendsSoftware\LoxPHP\Scanner\Token\Token;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\Type\TokenType;
use function array_values;
use function sprintf;

class Parser implements ParserInterface
{
    /**
     * Current token position.
     *
     * @var int
     */
    private int $current = 0;

    /**
     * Scanned tokens.
     *
     * @var array<int, TokenInterface>
     */
    private array $tokens;

    /**
     * @inheritDoc
     */
    public function parse(array $tokens): array
    {
        $this->tokens = array_values($tokens);

        $statements = [];
        while (!$this->isAtEnd()) {
            $statements[] = $this->declaration();
        }

        return $statements;
    }

    /**
     * Declaration.
     *
     * @throws ParseError
     */
    private function declaration(): StatementInterface
    {
        if ($this->match(TokenType::NEW_CLASS)) {
            return $this->classDeclaration();
        }
        if ($this->check(TokenType::FUN) && $this->checkNext(TokenType::IDENTIFIER)) {
            $this->advance();

            return $this->functionDeclaration('function');
        }
        if ($this->match(TokenType::VAR)) {
            return $this->variableDeclaration();
        }

        return $this->statement();
    }

    /**
     * Class statement.
     *
     * @return StatementInterface
     * @throws ParseError
     */
    private function classDeclaration(): StatementInterface
    {
        $name = $this->consume(TokenType::IDENTIFIER, "Expect class name.");

        $superclass = null;
        if ($this->match(TokenType::LESS)) {
            $this->consume(TokenType::IDENTIFIER, "Expect superclass name.");
            $superclass = new VariableExpression($this->previous());
        }

        $this->consume(TokenType::LEFT_BRACE, "Expect '{' before class body.");

        $methods = [];
        while (!$this->check(TokenType::RIGHT_BRACE) && !$this->isAtEnd()) {
            $methods[] = $this->functionDeclaration('method');
        }

        $this->consume(TokenType::RIGHT_BRACE, "Expect '}' after body class.'");

        return new ClassStatement($name, $superclass, $methods);
    }

    /**
     * Function declaration.
     *
     * @param string $type
     *
     * @return FunctionStatement
     * @throws ParseError
     */
    private function functionDeclaration(string $type): FunctionStatement
    {
        $name = $this->consume(TokenType::IDENTIFIER, sprintf('Expect %s name.', $type));

        return new FunctionStatement($name, $this->functionBody($type));
    }

    /**
     * Function body.
     *
     * @param string $type
     *
     * @return FunctionExpression
     * @throws ParseError
     */
    private function functionBody(string $type): FunctionExpression
    {
        $this->consume(TokenType::LEFT_PAREN, sprintf("Expect '(' after %s name'", $type));

        $parameters = [];
        if (!$this->check(TokenType::RIGHT_PAREN)) {
            do {
                if (count($parameters) >= 255) {
                    $token = $this->current();
                    throw new ParseError(
                        "Can't have more than 255 parameters.",
                        $token->getLine(),
                        $token->getColumn()
                    );
                }

                $parameters[] = $this->consume(TokenType::IDENTIFIER, 'Expect parameter name.');
            } while ($this->match(TokenType::COMMA));
        }

        $this->consume(TokenType::RIGHT_PAREN, sprintf("Expect ')' after %s parameters.", $type));
        $this->consume(TokenType::LEFT_BRACE, sprintf("Expect '{' before %s body.", $type));

        $body = $this->blockStatement();

        return new FunctionExpression($parameters, $body);
    }

    /**
     * Variable declaration.
     *
     * @return StatementInterface
     * @throws ParseError
     */
    private function variableDeclaration(): StatementInterface
    {
        $name = $this->consume(TokenType::IDENTIFIER, 'Expect variable name.');

        $initializer = null;
        if ($this->match(TokenType::EQUAL)) {
            $initializer = $this->expression();
        }

        $this->consume(TokenType::SEMICOLON, "Expect ';' after variable declaration.");

        return new VariableStatement($name, $initializer);
    }

    /**
     * Statement.
     *
     * @return StatementInterface
     * @throws ParseError
     */
    private function statement(): StatementInterface
    {
        if ($this->match(TokenType::FOR)) {
            return $this->forStatement();
        }
        if ($this->match(TokenType::IF)) {
            return $this->ifStatement();
        }
        if ($this->match(TokenType::RETURN)) {
            return $this->returnStatement();
        }
        if ($this->match(TokenType::WHILE)) {
            return $this->whileStatement();
        }
        if ($this->match(TokenType::LEFT_BRACE)) {
            return new BlockStatement($this->blockStatement());
        }

        return $this->expressionStatement();
    }

    /**
     * If statement.
     *
     * @return StatementInterface
     * @throws ParseError
     */
    private function ifStatement(): StatementInterface
    {
        $this->consume(TokenType::LEFT_PAREN, "Expect '(' after 'if''.");
        $condition = $this->expression();
        $this->consume(TokenType::RIGHT_PAREN, "Expect ')' after if condition.");

        $then = $this->statement();

        $else = null;
        if ($this->match(TokenType::ELSE)) {
            $else = $this->statement();
        }

        return new IfStatement($condition, $then, $else);
    }

    /**
     * Return statement.
     *
     * @return StatementInterface
     * @throws ParseError
     */
    private function returnStatement(): StatementInterface
    {
        $keyword = $this->previous();
        $value = null;
        if (!$this->check(TokenType::SEMICOLON)) {
            $value = $this->expression();
        }

        $this->consume(TokenType::SEMICOLON, "Expect ';' after return value.");

        return new ReturnStatement($keyword, $value);
    }

    /**
     * Block statement.
     *
     * @return array<StatementInterface>
     * @throws ParseError
     */
    private function blockStatement(): array
    {
        $statements = [];
        while (!$this->check(TokenType::RIGHT_BRACE) && !$this->isAtEnd()) {
            $statements[] = $this->declaration();
        }

        $this->consume(TokenType::RIGHT_BRACE, "Expect '}' after block.");

        return $statements;
    }

    /**
     * Expression statement.
     *
     * @return StatementInterface
     * @throws ParseError
     */
    private function expressionStatement(): StatementInterface
    {
        $expression = $this->expression();
        $this->consume(TokenType::SEMICOLON, "Expect ';' after expression.");

        return new ExpressionStatement($expression);
    }

    /**
     * While statement.
     *
     * @return StatementInterface
     * @throws ParseError
     */
    private function whileStatement(): StatementInterface
    {
        $this->consume(TokenType::LEFT_PAREN, "Expect '(' after while.");
        $condition = $this->expression();
        $this->consume(TokenType::RIGHT_PAREN, "Expect ')' after condition.");
        $body = $this->statement();

        return new WhileStatement($condition, $body);
    }

    /**
     * For statement.
     *
     * @return StatementInterface
     * @throws ParseError
     */
    private function forStatement(): StatementInterface
    {
        $this->consume(TokenType::LEFT_PAREN, "Expect '(' after for.");

        if ($this->match(TokenType::SEMICOLON)) {
            $initializer = null;
        } elseif ($this->match(TokenType::VAR)) {
            $initializer = $this->variableDeclaration();
        } else {
            $initializer = $this->expressionStatement();
        }

        $condition = null;
        if (!$this->check(TokenType::SEMICOLON)) {
            $condition = $this->expression();
        }

        $this->consume(TokenType::SEMICOLON, "Expect ';' after loop condition.");

        $increment = null;
        if (!$this->check(TokenType::RIGHT_PAREN)) {
            $increment = $this->expression();
        }

        $this->consume(TokenType::RIGHT_PAREN, "Expect ')' after for clauses.");

        $body = $this->statement();
        if ($increment) {
            $body = new BlockStatement([
                $body,
                new ExpressionStatement($increment),
            ]);
        }

        if (!$condition) {
            $condition = new LiteralExpression(TokenType::TRUE, true);
        }

        $body = new WhileStatement($condition, $body);
        if ($initializer) {
            $body = new BlockStatement([$initializer, $body]);
        }

        return $body;
    }

    /**
     * Check if current position matches any of the token types.
     *
     * @param TokenType ...$types
     *
     * @return bool
     */
    private function match(TokenType...$types): bool
    {
        foreach ($types as $type) {
            if ($this->check($type)) {
                $this->advance();
                return true;
            }
        }

        return false;
    }

    /**
     * Check if current position matches token type.
     *
     * @param TokenType $type
     *
     * @return bool
     */
    private function check(TokenType $type): bool
    {
        if ($this->isAtEnd()) {
            return false;
        }

        return $this->current()->getType() === $type;
    }

    /**
     * Check if next position matches token type.
     *
     * @param TokenType $type
     *
     * @return bool
     */
    private function checkNext(TokenType $type): bool
    {
        $next = $this->next();
        if ($this->isAtEnd() || !$next) {
            return false;
        }

        if ($next->getType() === TokenType::EOF) {
            return false;
        }

        return $this->tokens[$this->current + 1]->getType() === $type;
    }

    /**
     * Move to next token and return current.
     *
     * @return TokenInterface
     */
    private function advance(): TokenInterface
    {
        if (!$this->isAtEnd()) {
            $this->current++;
        }

        return $this->previous();
    }

    /**
     * Consume token.
     *
     * @param TokenType $expected
     * @param string    $reason
     *
     * @return TokenInterface
     * @throws ParseError When current token is not of expected type.
     */
    private function consume(TokenType $expected, string $reason): TokenInterface
    {
        if ($this->check($expected)) {
            return $this->advance();
        }

        $token = $this->current();
        throw new ParseError($reason, $token->getLine(), $token->getColumn());
    }

    /**
     * Check if current token matches EOF token type.
     *
     * @return bool
     */
    private function isAtEnd(): bool
    {
        return $this->current()->getType() === TokenType::EOF;
    }

    /**
     * Get next token.
     *
     * @return TokenInterface
     */
    private function current(): TokenInterface
    {
        return $this->tokens[$this->current];
    }

    /**
     * Get previous token.
     *
     * @return TokenInterface
     */
    private function previous(): TokenInterface
    {
        return $this->tokens[$this->current - 1];
    }

    /**
     * Get next token.
     *
     * @return TokenInterface|null
     */
    private function next(): ?TokenInterface
    {
        return $this->tokens[$this->current + 1] ?? null;
    }

    /**
     * Expression grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function expression(): ExpressionInterface
    {
        return $this->assignment();
    }

    /**
     * Assignment grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function assignment(): ExpressionInterface
    {
        $expression = $this->or();
        if ($this->match(
            TokenType::EQUAL,
            TokenType::PLUS_EQUAL,
            TokenType::MINUS_EQUAL,
            TokenType::SLASH_EQUAL,
            TokenType::STAR_EQUAL,
            TokenType::MODULO_EQUAL,
        )) {
            $operator = $this->previous();
            $assignment = $this->assignment();

            if ($expression instanceof VariableExpression) {
                $type = match ($operator->getType()) {
                    TokenType::PLUS_EQUAL => TokenType::PLUS,
                    TokenType::MINUS_EQUAL => TokenType::MINUS,
                    TokenType::SLASH_EQUAL => TokenType::SLASH,
                    TokenType::STAR_EQUAL => TokenType::STAR,
                    TokenType::MODULO_EQUAL => TokenType::MODULO,
                    default => TokenType::EQUAL
                };

                if ($type !== TokenType::EQUAL) {
                    $assignment = new BinaryExpression(
                        $expression,
                        new Token($type, $operator->getLine(), $operator->getColumn(), $operator->getLexeme()),
                        $assignment
                    );
                }

                return new AssignExpression($expression->getName(), $assignment);
            } elseif ($expression instanceof GetExpression) {
                return new SetExpression($expression->getObject(), $expression->getName(), $assignment);
            }

            throw new ParseError('Invalid assignment target.', $operator->getLine(), $operator->getColumn());
        }

        return $expression;
    }

    /**
     * Or grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function or(): ExpressionInterface
    {
        $expression = $this->and();

        while ($this->match(TokenType::OR)) {
            $operator = $this->previous();
            $right = $this->and();
            $expression = new LogicalExpression($expression, $operator, $right);
        }

        return $expression;
    }

    /**
     * And grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function and(): ExpressionInterface
    {
        $expression = $this->equality();

        while ($this->match(TokenType::AND)) {
            $operator = $this->previous();
            $right = $this->equality();
            $expression = new LogicalExpression($expression, $operator, $right);
        }

        return $expression;
    }

    /**
     * Equality grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function equality(): ExpressionInterface
    {
        $expression = $this->comparison();
        while ($this->match(TokenType::BANG_EQUAL, TokenType::EQUAL_EQUAL)) {
            $expression = new BinaryExpression($expression, $this->previous(), $this->comparison());
        }

        return $expression;
    }

    /**
     * Comparison grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function comparison(): ExpressionInterface
    {
        $expression = $this->term();
        while ($this->match(TokenType::GREATER, TokenType::GREATER_EQUAL, TokenType::LESS, TokenType::LESS_EQUAL)) {
            $expression = new BinaryExpression($expression, $this->previous(), $this->term());
        }

        return $expression;
    }

    /**
     * Term grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function term(): ExpressionInterface
    {
        $expression = $this->factor();
        while ($this->match(TokenType::MINUS, TokenType::PLUS)) {
            $expression = new BinaryExpression($expression, $this->previous(), $this->factor());
        }

        return $expression;
    }

    /**
     * Factor grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function factor(): ExpressionInterface
    {
        $expression = $this->typeof();
        while ($this->match(TokenType::SLASH, TokenType::STAR, TokenType::MODULO)) {
            $expression = new BinaryExpression($expression, $this->previous(), $this->typeof());
        }

        return $expression;
    }

    /**
     * Typeof grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function typeof(): ExpressionInterface
    {
        if ($this->match(TokenType::TYPEOF)) {
            return new TypeofExpression($this->unary());
        }

        return $this->unary();
    }

    /**
     * Unary grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function unary(): ExpressionInterface
    {
        if ($this->match(TokenType::BANG, TokenType::MINUS)) {
            return new UnaryExpression($this->previous(), $this->unary());
        }

        return $this->call();
    }

    /**
     * Call grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function call(): ExpressionInterface
    {
        $expression = $this->primary();

        while (true) {
            if ($this->match(TokenType::LEFT_PAREN)) {
                $arguments = [];
                if (!$this->check(TokenType::RIGHT_PAREN)) {
                    do {
                        if (count($arguments) >= 255) {
                            $token = $this->current();

                            throw new ParseError(
                                "Can't have more than 255 arguments.",
                                $token->getLine(),
                                $token->getColumn()
                            );
                        }

                        $arguments[] = $this->expression();
                    } while ($this->match(TokenType::COMMA));
                }

                $expression = new CallExpression(
                    $expression,
                    $this->consume(TokenType::RIGHT_PAREN, "Expect ')' after arguments."),
                    $arguments
                );
            } elseif ($this->match(TokenType::DOT)) {
                $expression = new GetExpression(
                    $expression,
                    $this->consume(TokenType::IDENTIFIER, "Expect property name after '.'.")
                );
            } else {
                break;
            }
        }

        return $expression;
    }

    /**
     * Primary grammar rule.
     *
     * @return ExpressionInterface
     * @throws ParseError
     */
    private function primary(): ExpressionInterface
    {
        if ($this->match(TokenType::FALSE)) {
            return new LiteralExpression(TokenType::FALSE, false);
        }

        if ($this->match(TokenType::TRUE)) {
            return new LiteralExpression(TokenType::TRUE, true);
        }

        if ($this->match(TokenType::NIL)) {
            return new LiteralExpression(TokenType::NIL, null);
        }

        if ($this->match(TokenType::NUMBER)) {
            return new LiteralExpression(TokenType::NUMBER, $this->previous()->getLexeme());
        }

        if ($this->match(TokenType::STRING)) {
            return new LiteralExpression(TokenType::STRING, $this->previous()->getLexeme());
        }

        if ($this->match(TokenType::SUPER)) {
            $keyword = $this->previous();
            $this->consume(TokenType::DOT, "Expect '.' after 'super'.");
            $method = $this->consume(TokenType::IDENTIFIER, 'Expect superclass method name.');

            return new SuperExpression($keyword, $method);
        }

        if ($this->match(TokenType::THIS)) {
            return new ThisExpression($this->previous());
        }

        if ($this->match(TokenType::IDENTIFIER)) {
            return new VariableExpression($this->previous());
        }

        if ($this->match(TokenType::LEFT_PAREN)) {
            $expression = $this->expression();
            $this->consume(TokenType::RIGHT_PAREN, "Expect ')' after expression.");

            return new GroupingExpression($expression);
        }

        if ($this->match(TokenType::LEFT_BRACKET)) {
            $arguments = [];
            if (!$this->check(TokenType::RIGHT_BRACKET)) {
                do {
                    $arguments[] = $this->expression();
                } while ($this->match(TokenType::COMMA));
            }

            $this->consume(TokenType::RIGHT_BRACKET, "Expect ']' after array items.");

            return new ArrayExpression($arguments);
        }

        if ($this->match(TokenType::FUN)) {
            return $this->functionBody('anonymous function');
        }

        $token = $this->current();
        throw new ParseError('Expression expected.', $token->getLine(), $token->getColumn());
    }
}
