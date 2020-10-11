<?php
declare(strict_types = 1);

namespace Codeception\Extension\PhantomJsServer;


interface PhantomJsServerInterface
{
    /**
     * @return resource|null
     */
    public function getRessource();

    /**
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * @param string $command
     * @param array $descriptorSpec
     */
    public function startServer(string $command, array $descriptorSpec): void;

    /**
     * @return bool
     */
    public function stopServer(): bool;

    /**
     * @param int $port
     *
     * @return bool
     */
    public function waitUntilServerIsUp(int $port): bool;
}
