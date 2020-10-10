<?php declare(strict_types = 1);

namespace Codeception\Extension\Configurator;

use Codeception\Exception\ExtensionException;

class Configurator implements ConfiguratorInterface
{
    public const DEFAULT_PATH = 'vendor/bin/phantomjs';
    public const DEFAULT_DEBUG = false;
    public const DEFAULT_PORT = 4444;

    /**
     * @var string
     */
    private string $os;

    /**
     * @param string $os
     */
    public function __construct(string $os)
    {
        $this->os = $os;
    }

    /**
     * @inheritDoc
     */
    public function configureExtension(array $config): array
    {
        $config = $this->findExecutable($config);
        $config = $this->setDebug($config);
        $config = $this->setPort($config);


        return $config;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function findExecutable(array $config): array
    {
        if($this->isWindows() && strpos($config['path'], '.exe') === false && file_exists(realpath($config['path'].'.exe'))) {
            $config['path'] .= '.exe';
        }

        if(!isset($config['path']) || empty($config['path'])) {
            $config['path'] = static::DEFAULT_PATH;
        }

        if(!file_exists(realpath($config['path']))) {
            throw new ExtensionException($this, "PhantomJS executable not found: {$config['path']}");
        }

        return $config;
    }

    /**
     * @return bool
     */
    private function isWindows(): bool
    {
        return strpos($this->os, 'WIN') !== false;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function setDebug(array $config): array
    {
        if(!isset($config['debug']) || empty($config['debug'])) {
            $config['debug'] = static::DEFAULT_DEBUG;
        }

        return $config;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function setPort(array $config): array
    {
        if(!isset($config['port'])) {
            $config['port'] = static::DEFAULT_PORT;
        }

        return $config;
    }
}
