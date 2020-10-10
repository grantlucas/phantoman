<?php declare(strict_types = 1);

namespace Codeception\Extension;

use Codeception\Extension\CommandHandler\CommandHandler;
use Codeception\Extension\CommandHandler\CommandHandlerInterface;
use Codeception\Extension\CommandHandler\Mapper\CommandMapper;
use Codeception\Extension\CommandHandler\Mapper\CommandMapperInterface;
use Codeception\Extension\Configurator\Configurator;
use Codeception\Extension\Configurator\ConfiguratorInterface;
use Codeception\Extension\PhantomJsServer\PhantomJsServer;
use Codeception\Extension\PhantomJsServer\PhantomJsServerInterface;

class PhantomanFactory
{
    /**
     * @return \Codeception\Extension\Configurator\ConfiguratorInterface
     */
    public function createConfigurator(): ConfiguratorInterface
    {
        return new Configurator();
    }

    /**
     * @return \Codeception\Extension\CommandHandler\CommandHandlerInterface
     */
    public function createCommandHandler(): CommandHandlerInterface
    {
        return new CommandHandler($this->createCommandMapper());
    }

    /**
     * @return \Codeception\Extension\CommandHandler\Mapper\CommandMapperInterface
     */
    public function createCommandMapper(): CommandMapperInterface
    {
        return new CommandMapper();
    }

    /**
     * @return \Codeception\Extension\PhantomJsServer\PhantomJsServerInterface
     */
    public function createPhantomJsServer(): PhantomJsServerInterface
    {
        return new PhantomJsServer();
    }
}
