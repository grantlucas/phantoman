<?php declare(strict_types = 1);

namespace Codeception\Extension\Configurator;

interface ConfiguratorInterface
{
    /**
     * @param array $config
     *
     * @return array
     */
    public function configureExtension(array $config): array;
}
