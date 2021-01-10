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

class PhantomanFactory implements PhantomanFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createConfigurator(): ConfiguratorInterface
    {
        return new Configurator(PHP_OS_FAMILY);
    }

    /**
     * @inheritDoc
     */
    public function createCommandHandler(): CommandHandlerInterface
    {
        return new CommandHandler($this->createCommandMapper());
    }

    /**
     * @inheritDoc
     */
    public function createCommandMapper(): CommandMapperInterface
    {
        return new CommandMapper();
    }

    /**
     * @inheritDoc
     */
    public function createPhantomJsServer(): PhantomJsServerInterface
    {
        return new PhantomJsServer();
    }
}
