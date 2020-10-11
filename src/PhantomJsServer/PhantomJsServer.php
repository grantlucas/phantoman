<?php declare(strict_types = 1);

namespace Codeception\Extension\PhantomJsServer;

use Codeception\Exception\ExtensionException;
use Codeception\Extension\CommandHandler\CommandHandlerInterface;

class PhantomJsServer implements PhantomJsServerInterface
{
    /**
     * @var resource|null
     */
    private $resource;

    /**
     * File pointers that correspond to PHP's end of any pipes that are created.
     *
     * @var array
     */
    private array $pipes;

    /**
     * @inheritDoc
     */
    public function getRessource()
    {
        return $this->resource;
    }

    /**
     * @inheritDoc
     */
    public function isRunning(): bool
    {
        return $this->resource !== null;
    }

    /**
     * @inheritDoc
     */
    public function startServer(string $command, array $descriptorSpec): void
    {
        $this->resource = proc_open($command, $descriptorSpec, $this->pipes, null, null, ['bypass_shell' => true]);

        if(!is_resource($this->resource) || !proc_get_status($this->resource)['running']) {
            proc_close($this->resource);
            throw new ExtensionException($this, 'Failed to start PhantomJS server.');
        }
    }

    /**
     * @inheritDoc
     */
    public function stopServer(): bool
    {
        // Terminate the process.
        // Note: Use of SIGINT adds dependency on PCTNL extension so we
        // use the integer value instead.
        proc_terminate($this->resource, 2);

        $max_checks = 10;
        $checks     = 0;
        while(proc_get_status($this->resource)['running'] === true) {
            if($max_checks === $checks) {
                throw new ExtensionException($this, 'Failed to stop PhantomJS server.');
            }
            $checks++;
            sleep(1);
        }

        $this->resource = null;

        $this->closePipes();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function waitUntilServerIsUp(int $port): bool
    {
        $max_checks = 10;
        $checks     = 0;
        $fp         = false;

        while($fp === false) {
            if($checks >= $max_checks) {
                throw new ExtensionException($this, 'PhantomJS server never became reachable.');
            }
            $fp = @fsockopen('127.0.0.1', $port, $errCode, $errStr, 10);
            $checks++;
            sleep(1);
        }

        fclose($fp);

        return true;
    }

    private function closePipes(): void
    {
        foreach($this->pipes as $pipe) {
            if(is_resource($pipe)) {
                fclose($pipe);
            }
        }
    }
}
