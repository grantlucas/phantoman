<?php
declare(strict_types = 1);

namespace Codeception\Extension\CommandHandler;


interface CommandHandlerInterface
{
    /**
     * Get PhantomJS command.
     *
     * @param array $config
     *
     * @return string
     */
    public function getCommand(array $config): string;


}
