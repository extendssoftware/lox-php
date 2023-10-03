<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Scanner;

use ExtendsSoftware\LoxPHP\Scanner\Error\SyntaxError;
use ExtendsSoftware\LoxPHP\Scanner\Token\Token;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\Type\TokenType;

use function array_slice;
use function array_splice;
use function count;
use function ctype_alnum;
use function ctype_alpha;
use function ctype_digit;
use function implode;
use function mb_str_split;
use function strtolower;

class Scanner implements ScannerInterface
{
    /**
     * Split source by character.
     *
     * @var array<string>
     */
    private array $characters = [];

    /**
     * Line in source.
     *
     * @var int
     */
    private int $line = 1;

    /**
     * Column in line.
     *
     * @var int
     */
    private int $column = 1;

    /**
     * Start position in source for token.
     *
     * @var int
     */
    private int $start = 0;

    /**
     * Current position in source.
     *
     * @var int
     */
    private int $position = 0;

    /**
     * Parsed tokens.
     *
     * @var array<TokenInterface>
     */
    private array $tokens = [];

    /**
     * Keywords.
     *
     * @var array<string, TokenType>
     */
    private array $keywords = [
        'and' => TokenType::AND,
        'class' => TokenType::NEW_CLASS,
        'else' => TokenType::ELSE,
        'false' => TokenType::FALSE,
        'for' => TokenType::FOR,
        'fun' => TokenType::FUN,
        'if' => TokenType::IF,
        'nil' => TokenType::NIL,
        'or' => TokenType::OR,
        'return' => TokenType::RETURN,
        'super' => TokenType::SUPER,
        'this' => TokenType::THIS,
        'true' => TokenType::TRUE,
        'typeof' => TokenType::TYPEOF,
        'var' => TokenType::VAR,
        'while' => TokenType::WHILE,
    ];

    /**
     * Escape sequences.
     *
     * @var array<string, string>
     */
    private array $sequences = [
        '"' => '"',
        '\\' => '\\',
        'n' => "\n",
        'r' => "\r",
        't' => "\t",
    ];

    /**
     * @inheritDoc
     */
    public function scan(string $source): array
    {
        $this->characters = mb_str_split($source);

        while ($this->hasMore()) {
            $this->start = $this->position;
            $this->process();
        }

        $this->addToken(TokenType::EOF, 'EOF');

        $tokens = $this->tokens;

        $this->reset();

        return $tokens;
    }

    /**
     * Check if there are more characters to consume.
     *
     * @return bool
     */
    private function hasMore(): bool
    {
        return $this->position < count($this->characters);
    }

    /**
     * Process next available token.
     *
     * @return void
     * @throws SyntaxError
     */
    private function process(): void
    {
        $character = $this->consume();
        switch ($character) {
            case '(':
                $this->addToken(TokenType::LEFT_PAREN);
                break;
            case ')':
                $this->addToken(TokenType::RIGHT_PAREN);
                break;
            case '{':
                $this->addToken(TokenType::LEFT_BRACE);
                break;
            case '}':
                $this->addToken(TokenType::RIGHT_BRACE);
                break;
            case '[':
                $this->addToken(TokenType::LEFT_BRACKET);
                break;
            case ']':
                $this->addToken(TokenType::RIGHT_BRACKET);
                break;
            case ',':
                $this->addToken(TokenType::COMMA);
                break;
            case '.':
                $this->addToken(TokenType::DOT);
                break;
            case ';':
                $this->addToken(TokenType::SEMICOLON);
                break;
            case ':':
                $this->addToken(TokenType::COLON);
                break;
            case '%':
                $this->addToken($this->match('=') ? TokenType::MODULO_EQUAL : TokenType::MODULO);
                break;
            case '-':
                $this->addToken($this->match('=') ? TokenType::MINUS_EQUAL : TokenType::MINUS);
                break;
            case '+':
                $this->addToken($this->match('=') ? TokenType::PLUS_EQUAL : TokenType::PLUS);
                break;
            case '*':
                $this->addToken($this->match('=') ? TokenType::STAR_EQUAL : TokenType::STAR);
                break;
            case '!':
                $this->addToken($this->match('=') ? TokenType::BANG_EQUAL : TokenType::BANG);
                break;
            case '=':
                $this->addToken($this->match('=') ? TokenType::EQUAL_EQUAL : TokenType::EQUAL);
                break;
            case '<':
                $this->addToken($this->match('=') ? TokenType::LESS_EQUAL : TokenType::LESS);
                break;
            case '>':
                $this->addToken($this->match('=') ? TokenType::GREATER_EQUAL : TokenType::GREATER);
                break;
            case '?':
                $this->addToken($this->match('.') ? TokenType::QUESTION_DOT : TokenType::QUESTION);
                break;
            case '/':
                if ($this->match('/')) {
                    // Consume second slash for comment.
                    $this->consume();

                    // A single line comment goes until the end of the line.
                    while ($this->current() !== "\n" && $this->hasMore()) {
                        $this->consume();
                    }
                } elseif ($this->match('*')) {
                    // Consume asterisk slash for comment.
                    $this->consume();

                    // A multiline comment goes until the closing asterisk + forward slash combination.
                    // @phpstan-ignore-next-line
                    while (!($this->match('*') && $this->match('/')) && $this->hasMore()) {
                        $this->consume();
                    }

                    if (!$this->hasMore()) {
                        throw new SyntaxError('Unterminated comment', $this->line, $this->column);
                    }
                } elseif ($this->match('=')) {
                    $this->addToken(TokenType::SLASH_EQUAL);
                } else {
                    $this->addToken(TokenType::SLASH);
                }
                break;
            case '"':
                while ($this->current() !== '"' && $this->hasMore()) {
                    if ($this->current() === '\\') {
                        $peek = $this->peek();
                        if (isset($this->sequences[$peek])) {
                            // Replace backslash and nex character with escape sequence.
                            array_splice($this->characters, $this->position, 2, [$this->sequences[$peek]]);
                        }
                    }

                    $this->consume();
                }

                if (!$this->hasMore()) {
                    throw new SyntaxError('Unterminated string', $this->line, $this->column);
                }

                // Consume the closing double quote.
                $this->consume();
                $this->addToken(
                    TokenType::STRING,
                    $this->getSubstring($this->start + 1, $this->position - 1)
                );
                break;
            case ' ':
            case "\r":
            case "\t":
            case "\n":
                // Ignore whitespace.
                break;
            default:
                if (ctype_digit($character)) {
                    while (ctype_digit($this->current())) {
                        $this->consume();
                    }

                    if ($this->current() === '.' && ctype_digit($this->peek())) {
                        // Consume the dot.
                        $this->consume();

                        while (ctype_digit($this->current())) {
                            $this->consume();
                        }
                    }

                    $this->addToken(
                        TokenType::NUMBER,
                        $this->getSubstring($this->start, $this->position)
                    );
                } elseif (ctype_alpha($character)) {
                    while (ctype_alnum($this->current()) || $this->current() === '_') {
                        $this->consume();
                    }

                    $lexeme = $this->getSubstring($this->start, $this->position);
                    $type = $this->keywords[strtolower($lexeme)] ?? TokenType::IDENTIFIER;
                    $this->addToken($type, $type === TokenType::IDENTIFIER ? $lexeme : null);
                } else {
                    throw new SyntaxError('Invalid character', $this->line, $this->column);
                }
                break;
        }
    }

    /**
     * Consume character and set pointer to next position.
     *
     * @return string
     */
    private function consume(): string
    {
        $consumed = $this->characters[$this->position++];
        if ($consumed === "\n") {
            $this->line++;
            $this->column = 1;
        } else {
            $this->column++;
        }

        return $consumed;
    }

    /**
     * Add token.
     *
     * @param TokenType $type
     * @param mixed $lexeme
     *
     * @return void
     */
    private function addToken(TokenType $type, mixed $lexeme = null): void
    {
        $this->tokens[] = new Token(
            $type,
            $this->line,
            $this->column - ($this->position - $this->start),
            $lexeme ?? $this->getSubstring($this->start, $this->position)
        );
    }

    /**
     * Get substring from source.
     *
     * @param int $start
     * @param int $end
     *
     * @return string
     */
    private function getSubstring(int $start, int $end): string
    {
        return implode('', array_slice($this->characters, $start, $end - $start));
    }

    /**
     * Consume character if it matched the expected character.
     *
     * @param string $expected
     *
     * @return bool
     */
    private function match(string $expected): bool
    {
        if (!$this->hasMore() || $this->current() !== $expected) {
            return false;
        }

        $this->consume();
        return true;
    }

    /**
     * Get current character.
     *
     * @return string
     */
    private function current(): string
    {
        return $this->characters[$this->position] ?? "\0";
    }

    /**
     * Peek next character.
     *
     * @return string
     */
    private function peek(): string
    {
        return $this->characters[$this->position + 1] ?? "\0";
    }

    /**
     * Reset scanner.
     *
     * @return void
     */
    private function reset(): void
    {
        $this->characters = [];
        $this->line = 1;
        $this->column = 1;
        $this->start = 0;
        $this->position = 0;
        $this->tokens = [];
    }
}
