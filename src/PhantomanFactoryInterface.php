<?php
declare(strict_types = 1);

namespace Codeception\Extension;


use Codeception\Extension\CommandHandler\CommandHandlerInterface;
use Codeception\Extension\CommandHandler\Mapper\CommandMapperInterface;
use Codeception\Extension\Configurator\ConfiguratorInterface;
use Codeception\Extension\PhantomJsServer\PhantomJsServerInterface;

interface PhantomanFactoryInterface
{
    /**
     * @return \Codeception\Extension\Configurator\ConfiguratorInterface
     */
    public function createConfigurator(): ConfiguratorInterface;

    /**
     * @return \Codeception\Extension\CommandHandler\CommandHandlerInterface
     */
    public function createCommandHandler(): CommandHandlerInterface;

    /**
     * @return \Codeception\Extension\CommandHandler\Mapper\CommandMapperInterface
     */
    public function createCommandMapper(): CommandMapperInterface;

    /**
     * @return \Codeception\Extension\PhantomJsServer\PhantomJsServerInterface
     */
    public function createPhantomJsServer(): PhantomJsServerInterface;
}
