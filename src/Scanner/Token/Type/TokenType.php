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
    case MINUS_EQUAL;
    case PLUS;
    case PLUS_EQUAL;
    case SEMICOLON;
    case COLON;
    case SLASH;
    case SLASH_EQUAL;
    case STAR;
    case STAR_EQUAL;
    case MODULO;
    case MODULO_EQUAL;
    case QUESTION;

    // One or two character tokens.
    case BANG;
    case BANG_EQUAL;
    case EQUAL;
    case EQUAL_EQUAL;
    case EQUAL_GREATER;
    case GREATER;
    case GREATER_EQUAL;
    case LESS;
    case LESS_EQUAL;
    case QUESTION_DOT;

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
    case FN;
    case FOR;
    case IF;
    case NIL;
    case OR;
    case RETURN;
    case SUPER;
    case THIS;
    case TRUE;
    case TYPEOF;
    case VAR;
    case WHILE;

    case EOF;
}
