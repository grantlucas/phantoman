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
    protected static array $events
        = [
            Events::SUITE_INIT => 'suiteInit',
            Events::SUITE_AFTER  => 'afterSuite',
        ];

    /**
     * @var \Codeception\Extension\PhantomanFactoryInterface
     */
    private PhantomanFactoryInterface $factory;

    /**
     * @var \Codeception\Extension\PhantomJsServer\PhantomJsServerInterface
     */
    private PhantomJsServer\PhantomJsServerInterface $server;

    /**
     * Phantoman constructor.
     *
     * @param array $config
     *   Current extension configuration.
     * @param array $options
     *   Passed running options.
     * @param null $factory
     */
    public function __construct(array $config, array $options, $factory = null)
    {
        // Codeception has an option called silent, which suppresses the console
        // output. Unfortunately there is no builtin way to activate this mode for
        // a single extension. This is why the option will passed from the
        // extension configuration ($config) to the global configuration ($options);
        // Note: This must be done before calling the parent constructor.
        if(isset($config['silent']) && $config['silent']) {
            $options['silent'] = true;
        }

        $this->factory = new PhantomanFactory();
        if($factory instanceof PhantomanFactoryInterface) {
            $this->factory = $factory;
        }

        parent::__construct($config, $options);

        $this->config = $this->factory->createConfigurator()->configureExtension($this->config);
        $this->server = $this->factory->createPhantomJsServer();
    }

    /**
     * Suite Init.
     *
     * @param \Codeception\Event\SuiteEvent $event
     *   The event with suite, result and settings.
     */
    public function suiteInit(SuiteEvent $event): void
    {
        if(!$this->isSuiteApplicable($event)) {
            return;
        }

        if($this->server->isRunning()) {
            return;
        }

        $command = $this->factory->createCommandHandler()->getCommand($this->config);

        if($this->config['debug']) {
            $this->writeln(PHP_EOL);

            // Output the generated command.
            $this->writeln('Generated PhantomJS Command:');
            $this->writeln($command);
            $this->writeln(PHP_EOL);
        }

        $descriptorSpec = [
            ['pipe', 'r'],
            ['file', $this->getLogDir().'phantomjs.output.txt', 'w'],
            ['file', $this->getLogDir().'phantomjs.errors.txt', 'a'],
        ];

        $this->writeln(PHP_EOL);
        $this->writeln('Starting PhantomJS Server.');

        $this->server->startServer($command, $this->config['path'], $descriptorSpec);

        $this->write('Waiting for the PhantomJS server to be reachable.');

        if($this->server->waitUntilServerIsUp($this->config['port'])) {
            $this->writeln('');
            $this->writeln('PhantomJS server now accessible.');
        }

        // Clear progress line writing.
        $this->writeln('');
    }

    /**
     * @param \Codeception\Event\SuiteEvent $event
     */
    public function afterSuite(SuiteEvent $event): void
    {
        if($this->server->isRunning()) {
            $this->write('Stopping PhantomJS Server.');

            if($this->server->stopServer()) {
                $this->writeln('');
                $this->writeln('PhantomJS server stopped.');
            }
        }
    }

    /**
     * @param \Codeception\Event\SuiteEvent $event
     *
     * @return bool
     */
    private function isSuiteApplicable(SuiteEvent $event): bool
    {
        return (in_array($event->getSuite()->getBaseName(), $this->config['suites'], true)
            && in_array($event->getSuite()->getName(), $this->config['suites'], true));
    }
}
