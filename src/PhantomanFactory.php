<?php
declare(strict_types = 1);

namespace Codeception\Extension;


use Codeception\Extension\Configurator\Configurator;
use Codeception\Extension\Configurator\ConfiguratorInterface;

class PhantomanFactory
{
    /**
     * @return \Codeception\Extension\Configurator\ConfiguratorInterface
     */
    public function createConfigurator(): ConfiguratorInterface
    {
        return new Configurator(PHP_OS_FAMILY);
    }
}
