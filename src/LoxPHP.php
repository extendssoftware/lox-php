<?php

declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP;

use ExtendsSoftware\LoxPHP\Interpreter\Interpreter;
use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Parser\Parser;
use ExtendsSoftware\LoxPHP\Parser\ParserInterface;
use ExtendsSoftware\LoxPHP\Resolver\Resolver;
use ExtendsSoftware\LoxPHP\Resolver\ResolverInterface;
use ExtendsSoftware\LoxPHP\Scanner\Scanner;
use ExtendsSoftware\LoxPHP\Scanner\ScannerInterface;

readonly class LoxPHP implements LoxPHPInterface
{
    /**
     * Lox constructor.
     *
     * @param ScannerInterface $scanner
     * @param ParserInterface $parser
     * @param ResolverInterface $resolver
     * @param InterpreterInterface $interpreter
     */
    public function __construct(
        private ScannerInterface $scanner = new Scanner(),
        private ParserInterface $parser = new Parser(),
        private ResolverInterface $resolver = new Resolver(),
        private InterpreterInterface $interpreter = new Interpreter()
    ) {
    }

    /**
     * @inheritDoc
     */
    public function run(string $source): void
    {
        $tokens = $this->scanner->scan($source);
        $statements = $this->parser->parseStatements($tokens);

        $this->resolver->resolveAll($statements);
        $this->interpreter->execute($statements);
    }
}
