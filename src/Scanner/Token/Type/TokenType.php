<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP\Scanner\Token\Type;

enum TokenType
{
    // Single-character tokens.
    case LEFT_PAREN;
    case RIGHT_PAREN;
    case LEFT_BRACE;
    case RIGHT_BRACE;
    case LEFT_BRACKET;
    case RIGHT_BRACKET;
    case COMMA;
    case DOT;
    case MINUS;
    case PLUS;
    case SEMICOLON;
    case SLASH;
    case STAR;

    // One or two character tokens.
    case BANG;
    case BANG_EQUAL;
    case EQUAL;
    case EQUAL_EQUAL;
    case GREATER;
    case GREATER_EQUAL;
    case LESS;
    case LESS_EQUAL;
    case QUESTION_SEMICOLON;
    case QUESTION_QUESTION;

    // Literals.
    case IDENTIFIER;
    case STRING;
    case NUMBER;

    // Keywords.
    case AND;
    case NEW_CLASS;
    case ELSE;
    case FALSE;
    case FUN;
    case FOR;
    case IF;
    case NIL;
    case OR;
    case PRINT;
    case RETURN;
    case SUPER;
    case THIS;
    case TRUE;
    case VAR;
    case WHILE;

    case EOF;
    case COMMENT;
}
