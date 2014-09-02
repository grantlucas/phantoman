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

        // Set default path for PhantomJS to "vendor/bin" for if it was installed via composer
        if (!isset($this->config['path'])) {
            $this->config['path'] = "vendor/bin";
        }

        // Set default WebDriver port
        if (!isset($this->config['port'])) {
            $this->config['port'] = 4444;
        }

        $this->startServer();

        $resource = $this->resource;
        register_shutdown_function(
            function () use ($resource) {
                if (is_resource($resource)) {
                    proc_terminate($resource);
                }
            }
        );
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

        $this->writeln("Starting PhantomJS Server");

        $command = $this->getCommand();

        $descriptorSpec = [
            ['pipe', 'r'],
            ['file', $this->getLogDir() . 'phantomjs.output.txt', 'w'],
            ['file', $this->getLogDir() . 'phantomjs.errors.txt', 'a']
            ];

        $this->resource = proc_open($command, $descriptorSpec, $this->pipes, null, null, ['bypass_shell' => true]);

        if (!is_resource($this->resource) || !proc_get_status($this->resource)['running']) {
            proc_close($this->resource);
            throw new ExtensionException($this, 'Failed to start PhantomJS server.');
        }

        // Wait till the server is reachable before continuing
        $max_checks = 10;
        $checks     = 0;

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
                if($i == $max_checks - 1 && proc_get_status($this->resource)['running'] == true) {
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
                    fclose($pipe);
                }
                proc_terminate($this->resource, 2);

                $this->write('.');

                // Wait before checking again
                sleep(1);
            }
        }
    }

    /**
     * Get PhantomJS command
     */
    private function getCommand()
    {
        $path = realpath($this->config['path']) . '/phantomjs';
        return escapeshellarg($path) . " --webdriver=" . $this->config['port'];
    }

    // methods that handle events
    public function beforeSuite(\Codeception\Event\SuiteEvent $e)
    {
        // Dummy function required to keep reference to this instance, otherwise Codeception would destroy it immediately
    }
}
