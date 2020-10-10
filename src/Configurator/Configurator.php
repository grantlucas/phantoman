<?php declare(strict_types = 1);

namespace Codeception\Extension\Configurator;

use Codeception\Exception\ExtensionException;

class Configurator implements ConfiguratorInterface
{
    /**
     * @inheritDoc
     */
    public function configureExtension(array $config): array
    {
        $config['path'] = $this->findExecutable($config['path']);
        $config['debug'] = $this->setDebug($config['debug']);
        $config['port'] = $this->setPort($config['port']);


        return $config;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function findExecutable(string $path = ''): string
    {
        if($this->isWindows() && file_exists(realpath($path.'.exe'))) {
            $path .= '.exe';
        }

        if(empty($path)) {
            $path = 'vendor/bin/phantomjs';
        }

        if(!file_exists(realpath($path))) {
            throw new ExtensionException($this, "PhantomJS executable not found: {$path}");
        }

        return $path;
    }

    /**
     * Checks if the current machine is Windows.
     *
     * @return bool
     */
    private function isWindows(): bool
    {
        return stripos(PHP_OS_FAMILY, 'WIN') === 0;
    }

    /**
     * @param bool $debug
     *
     * @return bool
     */
    private function setDebug(bool $debug): bool
    {
        if(empty($debug)) {
            return false;
        }

        return $debug;
    }

    private function setPort(int $port): int
    {
        // Set default WebDriver port.
        if(!isset($port)) {
            $port = 4444;
        }

        return $port;
    }
}
