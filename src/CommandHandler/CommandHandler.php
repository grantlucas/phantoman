<?php declare(strict_types = 1);

namespace Codeception\Extension\CommandHandler;

use Codeception\Extension\CommandHandler\Mapper\CommandMapperInterface;

class CommandHandler implements CommandHandlerInterface
{
    /**
     * @var \Codeception\Extension\CommandHandler\Mapper\CommandMapperInterface
     */
    private $commandMapper;

    /**
     * @param \Codeception\Extension\CommandHandler\Mapper\CommandMapperInterface $commandMapper
     */
    public function __construct(CommandMapperInterface $commandMapper)
    {
        $this->commandMapper = $commandMapper;
    }

    /**
     * @inheritDoc
     */
    public function getCommand(array $config): string
    {
        $commandPrefix = $this->getPrefix($config['path']);

        return $commandPrefix.
            escapeshellarg(realpath($config['path'])).
            ' '.
            $this->commandMapper->mapParamsToCommandLineArgs($config);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getPrefix(string $path): string
    {
        if(strpos($path, 'exe') !== false) {
            return '';
        }

        return 'exec ';
    }
}
