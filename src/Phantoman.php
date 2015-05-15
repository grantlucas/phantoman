<?php
/**
 * Phantoman
 *
 * The Codeception extension for automatically starting and stopping PhantomJS
 * when running tests.
 *
 * Originally based off of PhpBuiltinServer Codeception extension
 * https://github.com/tiger-seo/PhpBuiltinServer
 */

namespace Codeception\Extension;

use Codeception\Exception\Extension as ExtensionException;

class Phantoman extends \Codeception\Platform\Extension
{

    // list events to listen to
    static $events = array(
        'suite.before' => 'beforeSuite',
    );

    private $resource;

    private $pipes;

    public function __construct($config, $options)
    {
        parent::__construct($config, $options);

        // Set default path for PhantomJS to "vendor/bin/phantomjs" for if it was installed via composer
        if (!isset($this->config['path'])) {
            $this->config['path'] = "vendor/bin/phantomjs";
        }

        // If a directory was provided for the path, use old method of appending PhantomJS
        if (is_dir(realpath($this->config['path']))) {
            // Show warning that this is being deprecated
            $this->writeLn("\r\n");
            $this->writeLn("WARNING: The PhantomJS path for Phantoman is set to a directory, this is being deprecated in the future. Please update your Phantoman configuration to be the full path to PhantomJS.");

            $this->config['path'] .= '/phantomjs';
        }

        // Add .exe extension if running on the windows
        if ($this->isWindows() && file_exists(realpath($this->config['path'] . '.exe'))) {
            $this->config['path'] .= '.exe';
        }

        // Set default WebDriver port
        if (!isset($this->config['port'])) {
            $this->config['port'] = 4444;
        }

        $this->startServer();
    }

    public function __destruct()
    {
        $this->stopServer();
    }

    /**
     * Start PhantomJS server
     */
    private function startServer()
    {
        if ($this->resource !== null) {
            return;
        }

        $this->writeLn("\r\n");
        $this->writeln("Starting PhantomJS Server");

        $command = $this->getCommand();

        $descriptorSpec = array(
            array('pipe', 'r'),
            array('file', $this->getLogDir() . 'phantomjs.output.txt', 'w'),
            array('file', $this->getLogDir() . 'phantomjs.errors.txt', 'a')
        );

        $this->resource = proc_open($command, $descriptorSpec, $this->pipes, null, null, array('bypass_shell' => true));

        if (!is_resource($this->resource) || !proc_get_status($this->resource)['running']) {
            proc_close($this->resource);
            throw new ExtensionException($this, 'Failed to start PhantomJS server.');
        }

        // Wait till the server is reachable before continuing
        $max_checks = 10;
        $checks = 0;

        $this->write("Waiting for the PhantomJS server to be reachable");
        while (true) {
            if ($checks >= $max_checks) {
                throw new ExtensionException($this, 'PhantomJS server never became reachable');
                break;
            }

            if ($fp = @fsockopen('127.0.0.1', $this->config['port'], $errCode, $errStr, 10)) {
                $this->writeln('');
                $this->writeln("PhantomJS server now accessible");
                fclose($fp);
                break;
            }

            $this->write('.');
            $checks++;

            // Wait before checking again
            sleep(1);
        }

        // Clear progress line writing
        $this->writeln('');
    }

    /**
     * Stop PhantomJS server
     */
    private function stopServer()
    {
        if ($this->resource !== null) {
            $this->write("Stopping PhantomJS Server");

            // Wait till the server has been stopped
            $max_checks = 10;
            for ($i = 0; $i < $max_checks; $i++) {
                // If we're on the last loop, and it's still not shut down, just
                // unset resource to allow the tests to finish
                if ($i == $max_checks - 1 && proc_get_status($this->resource)['running'] == true) {
                    $this->writeln('');
                    $this->writeln("Unable to properly shutdown PhantomJS server");
                    unset($this->resource);
                    break;
                }

                // Check if the process has stopped yet
                if (proc_get_status($this->resource)['running'] == false) {
                    $this->writeln('');
                    $this->writeln("PhantomJS server stopped");
                    unset($this->resource);
                    break;
                }

                foreach ($this->pipes as $pipe) {
                    if (!is_resource($pipe)) {
                        // The pipe can be closed in a previous attempt
                        continue;
                    }
                    fclose($pipe);
                }

                posix_kill(proc_get_status($this->resource)['pid'], 15);

                $this->write('.');

                // Wait before checking again
                sleep(1);
            }
        }
    }

    /**
     * getCommandParameters
     *
     * @return string
     */
    private function getCommandParameters()
    {
        $mapping = array(
            'proxy' => '--proxy',
            'proxyType' => '--proxy-type',
            'proxyAuth' => '--proxy-auth',
            'webSecurity' => '--web-security',
            'port' => '--webdriver',
            'ignoreSslErrors' => '--ignore-ssl-errors',
        );
        $params = array();
        foreach ($this->config as $configKey => $configValue) {
            if (!empty($mapping[$configKey])) {
                if (is_bool($configValue)) {
                    // Make sure the value is true/false and not 1/0
                    $configValue = ($configValue) ? 'true' : 'false';
                }
                $params[] = $mapping[$configKey] . '=' . $configValue;
            }
        }
        return implode(' ', $params);
    }

    /**
     * Get PhantomJS command
     */
    private function getCommand()
    {
        return escapeshellarg(realpath($this->config['path'])) . ' ' . $this->getCommandParameters();
    }

    /**
     * Checks if the current machine is Windows.
     *
     * @return bool True if the machine is windows.
     * @see http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows
     */
    private function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    // methods that handle events
    public function beforeSuite(\Codeception\Event\SuiteEvent $e)
    {
        // Dummy function required to keep reference to this instance, otherwise Codeception would destroy it immediately
    }
}
