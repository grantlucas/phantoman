<?php declare(strict_types = 1);

namespace Codeception\Extension\Configurator;


use Codeception\Exception\ExtensionException;
use Symfony\Component\DomCrawler\Field\InputFormField;

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
            throw new ExtensionException($this, "PhantomJS executable not found: {$this->config['path']}");
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
     * @param array $debug
     *
     * @return array
     */
    private function setDebug(string $debug): array
    {
        if(!isset($debug['debug'])) {
            $debug['debug'] = false;
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
