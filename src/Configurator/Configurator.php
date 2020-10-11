<?php declare(strict_types = 1);

namespace Codeception\Extension\Configurator;

use Codeception\Exception\ExtensionException;

class Configurator implements ConfiguratorInterface
{
    public const DEFAULT_PATH = 'vendor/bin/phantomjs';
    public const DEFAULT_DEBUG = false;
    public const DEFAULT_PORT = 4444;
    public const DEFAULT_SUITES = ['unit', 'functional', 'acceptance'];

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
        codecept_debug('Configuration started.');
        $config = $this->findExecutable($config);
        $config = $this->setDebug($config);
        $config = $this->setPort($config);
        $config = $this->formatSuites($config);

        codecept_debug('Configuration done.');

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

        codecept_debug("Path is {$config['path']}");
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

        codecept_debug(sprintf('Debug is set to %b', $config['debug']));
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

        codecept_debug("Port is {$config['port']}");
        return $config;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function formatSuites(array $config): array
    {
        if(isset($config['suites'])) {
            if(is_string($config['suites'])) {
                $config['suites'] = [$config['suites']];
                codecept_debug(sprintf('Suites is %s', implode(', ', $config['suites'])));
                return $config;
            }

            codecept_debug(sprintf('Suites is %s', implode(', ', $config['suites'])));
            return $config;
        }

        $config['suites'] = static::DEFAULT_SUITES;
        codecept_debug(sprintf('Suites is %s', implode(', ', $config['suites'])));
        return $config;
    }
}
