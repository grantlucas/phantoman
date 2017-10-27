<?php

namespace Codeception\Extension;

/**
 * Phantoman.
 *
 * The Codeception extension for automatically starting and stopping PhantomJS
 * when running tests.
 *
 * Originally based off of PhpBuiltinServer Codeception extension
 * https://github.com/tiger-seo/PhpBuiltinServer
 */

use Codeception\Event\SuiteEvent;
use Codeception\Events;
use Codeception\Exception\ExtensionException;
use Codeception\Extension;

/**
 * Class Phantoman.
 *
 * @package Codeception\Extension
 */
class Phantoman extends Extension
{

    /**
     * Events to listen to.
     *
     * @var array
     */
    protected static $events = [
        Events::SUITE_INIT => 'suiteInit',
    ];

    /**
     * A resource representing the PhantomJS process.
     *
     * @var resource
     */
    private $resource;

    /**
     * File pointers that correspond to PHP's end of any pipes that are created.
     *
     * @var array
     */
    private $pipes;

    /**
     * Phantoman constructor.
     *
     * @param array $config
     *   Current extension configuration.
     * @param array $options
     *   Passed running options.
     *
     * @throws \Codeception\Exception\ExtensionException
     */
    public function __construct(array $config, array $options)
    {
        // Codeception has an option called silent, which suppresses the console
        // output. Unfortunately there is no builtin way to activate this mode for
        // a single extension. This is why the option will passed from the
        // extension configuration ($config) to the global configuration ($options);
        // Note: This must be done before calling the parent constructor.
        if (isset($config['silent']) && $config['silent']) {
            $options['silent'] = true;
        }
        parent::__construct($config, $options);

        // Set default path for PhantomJS to "vendor/bin/phantomjs" for if it was
        // installed via composer.
        if (!isset($this->config['path'])) {
            $this->config['path'] = 'vendor/bin/phantomjs';
        }

        // Add .exe extension if running on the windows.
        if ($this->isWindows() && file_exists(realpath($this->config['path'] . '.exe'))) {
            $this->config['path'] .= '.exe';
        }

        if (!file_exists(realpath($this->config['path']))) {
            throw new ExtensionException($this, "PhantomJS executable not found: {$this->config['path']}");
        }

        // Set default WebDriver port.
        if (!isset($this->config['port'])) {
            $this->config['port'] = 4444;
        }

        // Set default debug mode.
        if (!isset($this->config['debug'])) {
            $this->config['debug'] = false;
        }
    }

    /**
     * Stop the server when we get destroyed.
     */
    public function __destruct()
    {
        $this->stopServer();
    }

    /**
     * Start PhantomJS server.
     *
     * @throws \Codeception\Exception\ExtensionException
     */
    private function startServer()
    {
        if ($this->resource !== null) {
            return;
        }

        $this->writeln(PHP_EOL);
        $this->writeln('Starting PhantomJS Server.');

        $command = $this->getCommand();

        if ($this->config['debug']) {
            $this->writeln(PHP_EOL);

            // Output the generated command.
            $this->writeln('Generated PhantomJS Command:');
            $this->writeln($command);
            $this->writeln(PHP_EOL);
        }

        $descriptorSpec = [
            ['pipe', 'r'],
            ['file', $this->getLogDir() . 'phantomjs.output.txt', 'w'],
            ['file', $this->getLogDir() . 'phantomjs.errors.txt', 'a'],
        ];

        $this->resource = proc_open($command, $descriptorSpec, $this->pipes, null, null, ['bypass_shell' => true]);

        if (!is_resource($this->resource) || !proc_get_status($this->resource)['running']) {
            proc_close($this->resource);
            throw new ExtensionException($this, 'Failed to start PhantomJS server.');
        }

        // Wait till the server is reachable before continuing.
        $max_checks = 10;
        $checks = 0;

        $this->write('Waiting for the PhantomJS server to be reachable.');
        while (true) {
            if ($checks >= $max_checks) {
                throw new ExtensionException($this, 'PhantomJS server never became reachable.');
            }

            $fp = @fsockopen('127.0.0.1', $this->config['port'], $errCode, $errStr, 10);
            if ($fp) {
                $this->writeln('');
                $this->writeln('PhantomJS server now accessible.');
                fclose($fp);
                break;
            }

            $this->write('.');
            $checks++;

            // Wait before checking again.
            sleep(1);
        }

        // Clear progress line writing.
        $this->writeln('');
    }

    /**
     * Stop PhantomJS server.
     */
    private function stopServer()
    {
        if ($this->resource !== null) {
            $this->write('Stopping PhantomJS Server.');

            // Wait till the server has been stopped.
            $max_checks = 10;
            for ($i = 0; $i < $max_checks; $i++) {
                // If we're on the last loop, and it's still not shut down, just
                // unset resource to allow the tests to finish.
                if ($i === $max_checks - 1 && proc_get_status($this->resource)['running'] === true) {
                    $this->writeln('');
                    $this->writeln('Unable to properly shutdown PhantomJS server.');
                    unset($this->resource);
                    break;
                }

                // Check if the process has stopped yet.
                if (proc_get_status($this->resource)['running'] === false) {
                    $this->writeln('');
                    $this->writeln('PhantomJS server stopped.');
                    unset($this->resource);
                    break;
                }

                foreach ($this->pipes as $pipe) {
                    if (is_resource($pipe)) {
                        fclose($pipe);
                    }
                }

                // Terminate the process.
                // Note: Use of SIGINT adds dependency on PCTNL extension so we
                // use the integer value instead.
                proc_terminate($this->resource, 2);

                $this->write('.');

                // Wait before checking again.
                sleep(1);
            }
        }
    }

    /**
     * Build the parameters for our command.
     *
     * @return string
     *   All parameters separated by spaces.
     */
    private function getCommandParameters()
    {
        // Map our config options to PhantomJS options.
        $mapping = [
            'port' => '--webdriver',
            'proxy' => '--proxy',
            'proxyType' => '--proxy-type',
            'proxyAuth' => '--proxy-auth',
            'webSecurity' => '--web-security',
            'ignoreSslErrors' => '--ignore-ssl-errors',
            'sslProtocol' => '--ssl-protocol',
            'sslCertificatesPath' => '--ssl-certificates-path',
            'remoteDebuggerPort' => '--remote-debugger-port',
            'remoteDebuggerAutorun' => '--remote-debugger-autorun',
            'cookiesFile' => '--cookies-file',
            'diskCache' => '--disk-cache',
            'maxDiskCacheSize' => '--max-disk-cache-size',
            'loadImages' => '--load-images',
            'localStoragePath' => '--local-storage-path',
            'localStorageQuota' => '--local-storage-quota',
            'localToRemoteUrlAccess' => '--local-to-remote-url-access',
            'outputEncoding' => '--output-encoding',
            'scriptEncoding' => '--script-encoding',
            'webdriverLoglevel' => '--webdriver-loglevel',
            'webdriverLogfile' => '--webdriver-logfile',
        ];

        $params = [];
        foreach ($this->config as $configKey => $configValue) {
            if (!empty($mapping[$configKey])) {
                if (is_bool($configValue)) {
                    // Make sure the value is true/false and not 1/0.
                    $configValue = $configValue ? 'true' : 'false';
                }
                $params[] = $mapping[$configKey] . '=' . $configValue;
            }
        }

        return implode(' ', $params);
    }

    /**
     * Get PhantomJS command.
     *
     * @return string
     *   Command to execute.
     */
    private function getCommand()
    {
        // Prefix command with exec on non Windows systems to ensure that we
        // receive the correct pid.
        // See http://php.net/manual/en/function.proc-get-status.php#93382
        $commandPrefix = $this->isWindows() ? '' : 'exec ';
        return $commandPrefix . escapeshellarg(realpath($this->config['path'])) . ' ' . $this->getCommandParameters();
    }

    /**
     * Checks if the current machine is Windows.
     *
     * @return bool
     *   True if the machine is windows.
     */
    private function isWindows()
    {
        return stripos(PHP_OS, 'WIN') === 0;
    }

    /**
     * Suite Init.
     *
     * @param \Codeception\Event\SuiteEvent $e
     *   The event with suite, result and settings.
     *
     * @throws \Codeception\Exception\ExtensionException
     */
    public function suiteInit(SuiteEvent $e)
    {
        // Check if PhantomJS should only be started for specific suites.
        if (isset($this->config['suites'])) {
            if (is_string($this->config['suites'])) {
                $suites = [$this->config['suites']];
            } else {
                $suites = $this->config['suites'];
            }

            // If the current suites aren't in the desired array, return without
            // starting PhantomJS.
            if (!in_array($e->getSuite()->getBaseName(), $suites, true)
                && !in_array($e->getSuite()->getName(), $suites, true)) {
                return;
            }
        }

        // Start the PhantomJS server.
        $this->startServer();
    }
}
