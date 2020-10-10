<?php declare(strict_types = 1);

namespace PhantomanTests\unit;

use Codeception\AssertThrows;
use Codeception\Event\SuiteEvent;
use Codeception\Exception\ExtensionException;
use Codeception\Extension\CommandHandler\CommandHandler;
use Codeception\Extension\Configurator\Configurator;
use Codeception\Extension\Phantoman;
use Codeception\Extension\PhantomanFactory;
use Codeception\Extension\PhantomJsServer\PhantomJsServer;
use Codeception\Suite;
use Codeception\Test\Unit;

class PhantomanTest extends Unit
{
    use AssertThrows;

    public function testSuiteInit(): void
    {
        $serverMock = $this->createMock(PhantomJsServer::class);
        $serverMock->expects(self::once())->method('isRunning')->willReturn(false);
        $serverMock->expects(self::once())->method('startServer');
        $serverMock->expects(self::once())->method('waitUntilServerIsUp')->willReturn(true);

        $commandHandlerMock = $this->createMock(CommandHandler::class);
        $commandHandlerMock->method('getCommand')->willReturn('command');

        $configuratorMock = $this->createMock(Configurator::class);
        $configuratorMock->method('configureExtension')->willReturn([
            'suites' => ['test', 'testing'],
            'debug'  => true,
            'path'   => 'test/path',
            'port'   => 1234,
        ]);

        $factoryMock = $this->createMock(PhantomanFactory::class);
        $factoryMock->method('createPhantomJsServer')->willReturn($serverMock);
        $factoryMock->method('createCommandHandler')->willReturn($commandHandlerMock);
        $factoryMock->method('createConfigurator')->willReturn($configuratorMock);

        $extension = new Phantoman([], ['silent' => true], $factoryMock);

        $suiteMock = $this->createMock(Suite::class);
        $suiteMock->method('getName')->willReturn('test');
        $suiteMock->method('getBaseName')->willReturn('test');
        $event = new SuiteEvent($suiteMock);

        $extension->suiteInit($event);
    }

    public function testSuiteInitFailure(): void
    {
        $serverMock = $this->createMock(PhantomJsServer::class);
        $serverMock->expects(self::once())->method('isRunning')->willReturn(false);
        $serverMock->expects(self::once())->method('startServer')->willThrowException(new ExtensionException(PhantomJsServer::class, 'Failed to start PhantomJS server.'));
        $serverMock->expects(self::never())->method('waitUntilServerIsUp')->willReturn(true);

        $commandHandlerMock = $this->createMock(CommandHandler::class);
        $commandHandlerMock->method('getCommand')->willReturn('command');

        $configuratorMock = $this->createMock(Configurator::class);
        $configuratorMock->method('configureExtension')->willReturn([
            'suites' => ['test', 'testing'],
            'debug'  => true,
            'path'   => 'test/path',
            'port'   => 1234,
        ]);

        $factoryMock = $this->createMock(PhantomanFactory::class);
        $factoryMock->method('createPhantomJsServer')->willReturn($serverMock);
        $factoryMock->method('createCommandHandler')->willReturn($commandHandlerMock);
        $factoryMock->method('createConfigurator')->willReturn($configuratorMock);

        $extension = new Phantoman([], ['silent' => true], $factoryMock);

        $suiteMock = $this->createMock(Suite::class);
        $suiteMock->method('getName')->willReturn('test');
        $suiteMock->method('getBaseName')->willReturn('test');
        $event = new SuiteEvent($suiteMock);

        $this->assertThrows(ExtensionException::class, function() use ($event, $extension){
            $extension->suiteInit($event);
        });
    }

    public function testAfterSuite(): void
    {
        $serverMock = $this->createMock(PhantomJsServer::class);
        $serverMock->expects(self::once())->method('isRunning')->willReturn(true);
        $serverMock->expects(self::once())->method('stopServer')->willReturn(true);

        $factoryMock = $this->createMock(PhantomanFactory::class);
        $factoryMock->method('createPhantomJsServer')->willReturn($serverMock);

        $extension = new Phantoman([], ['silent' => true], $factoryMock);

        $suiteMock = $this->createMock(Suite::class);
        $suiteMock->method('getName')->willReturn('test');
        $suiteMock->method('getBaseName')->willReturn('test');
        $event = new SuiteEvent($suiteMock);

        $extension->afterSuite($event);
    }

    public function testAfterSuiteFailure(): void
    {
        $serverMock = $this->createMock(PhantomJsServer::class);
        $serverMock->expects(self::once())->method('isRunning')->willReturn(true);
        $serverMock->expects(self::once())->method('stopServer')->willThrowException(new ExtensionException(PhantomJsServer::class, 'Failed to stop PhantomJS server.'));

        $factoryMock = $this->createMock(PhantomanFactory::class);
        $factoryMock->method('createPhantomJsServer')->willReturn($serverMock);

        $extension = new Phantoman([], ['silent' => true], $factoryMock);

        $suiteMock = $this->createMock(Suite::class);
        $suiteMock->method('getName')->willReturn('test');
        $suiteMock->method('getBaseName')->willReturn('test');
        $event = new SuiteEvent($suiteMock);

        $this->assertThrows(ExtensionException::class, function() use ($event, $extension){
            $extension->afterSuite($event);
        });
    }
}
