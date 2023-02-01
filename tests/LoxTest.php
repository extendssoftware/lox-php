<?php
declare(strict_types=1);

namespace ExtendsSoftware\LoxPHP;

use ExtendsSoftware\LoxPHP\Interpreter\InterpreterInterface;
use ExtendsSoftware\LoxPHP\Parser\ParserInterface;
use ExtendsSoftware\LoxPHP\Parser\Statement\StatementInterface;
use ExtendsSoftware\LoxPHP\Resolver\ResolverInterface;
use ExtendsSoftware\LoxPHP\Scanner\ScannerInterface;
use ExtendsSoftware\LoxPHP\Scanner\Token\TokenInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test of class LoxPHP.
 *
 * @package ExtendsSoftware\LoxPHP
 * @author  Vincent van Dijk <vincent@extends.nl>
 * @version 0.1.0
 * @see     https://github.com/extendssoftware/lox-php
 */
class LoxTest extends TestCase
{
    /**
     * Test that class will scan, parse, resolve and interpret source code.
     *
     * @covers \ExtendsSoftware\LoxPHP\LoxPHP::__construct()
     * @covers \ExtendsSoftware\LoxPHP\LoxPHP::run()
     * @return void
     */
    public function testRun(): void
    {
        $source = 'system.print((1 + 2) / 3);';

        $token = $this->createMock(TokenInterface::class);
        $statement = $this->createMock(StatementInterface::class);

        $scanner = $this->createMock(ScannerInterface::class);
        $scanner
            ->expects($this->once())
            ->method('scan')
            ->with($source)
            ->willReturn([$token, $token]);

        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->expects($this->once())
            ->method('parse')
            ->with([$token, $token])
            ->willReturn([$statement, $statement]);

        $resolver = $this->createMock(ResolverInterface::class);
        $resolver
            ->expects($this->once())
            ->method('resolveAll')
            ->with([$statement, $statement]);

        $interpreter = $this->createMock(InterpreterInterface::class);
        $interpreter
            ->expects($this->once())
            ->method('execute')
            ->with([$statement, $statement]);

        /**
         * @param ScannerInterface     $scanner
         * @param ParserInterface      $parser
         * @param ResolverInterface    $resolver
         * @param InterpreterInterface $interpreter
         */
        $lox = new LoxPHP($scanner, $parser, $resolver, $interpreter);
        $lox->run($source);
    }
}
