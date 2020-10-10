<?php declare(strict_types = 1);

namespace PhantomanTests\unit;

use Codeception\Extension\CommandHandler\CommandHandler;
use Codeception\Extension\CommandHandler\Mapper\CommandMapper;
use Codeception\Extension\Configurator\Configurator;
use Codeception\Test\Unit;

class CommandHandlerTest extends Unit
{
    public function testGetCommandUnix(): void
    {
        touch(Configurator::DEFAULT_PATH);
        $config = [
            'path'  => Configurator::DEFAULT_PATH,
            'debug' => Configurator::DEFAULT_DEBUG,
            'port'  => Configurator::DEFAULT_PORT,
        ];

        $mapper         = new CommandMapper();
        $commandHandler = new CommandHandler($mapper);

        $command = $commandHandler->getCommand($config);

        self::assertSame('exec \'' . realpath(Configurator::DEFAULT_PATH) . '\' --webdriver=4444', $command);
        unlink(Configurator::DEFAULT_PATH);
    }

    public function testGetCommandWindows(): void
    {
        touch(Configurator::DEFAULT_PATH.'.exe');
        $config = [
            'path'  => Configurator::DEFAULT_PATH.'.exe',
            'debug' => Configurator::DEFAULT_DEBUG,
            'port'  => Configurator::DEFAULT_PORT,
        ];

        $mapper         = new CommandMapper();
        $commandHandler = new CommandHandler($mapper);

        $command = $commandHandler->getCommand($config);

        self::assertSame('\'' . realpath(Configurator::DEFAULT_PATH . '.exe') . '\' --webdriver=4444', $command);
        unlink(Configurator::DEFAULT_PATH.'.exe');
    }
}
