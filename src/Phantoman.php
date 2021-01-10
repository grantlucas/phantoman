<?php

namespace Codeception\Extension;

use Codeception\Event\SuiteEvent;
use Codeception\Events;
use Codeception\Extension;

/**
 * Phantoman.
 *
 * The Codeception extension for automatically starting and stopping PhantomJS
 * when running tests.
 *
 * Originally based off of PhpBuiltinServer Codeception extension
 * https://github.com/tiger-seo/PhpBuiltinServer
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
    protected static $events
        = [
            Events::SUITE_BEFORE => 'suiteInit',
            Events::SUITE_AFTER  => 'afterSuite',
        ];

    /**
     * @var \Codeception\Extension\PhantomanFactoryInterface
     */
    private $factory;

    /**
     * @var \Codeception\Extension\PhantomJsServer\PhantomJsServerInterface
     */
    private $server;

    public function _initialize(): void
    {
        $this->factory = new PhantomanFactory();

        $this->config = $this->factory->createConfigurator()->configureExtension($this->config);
        $this->server = $this->factory->createPhantomJsServer();
    }

    /**
     * Phantoman constructor.
     *
     * @param array $config
     * @param array $options
     * @param \Codeception\Extension\PhantomanFactoryInterface|null $factory
     */
    public function __construct(array $config, array $options, $factory=null)
    {
        if($factory instanceof PhantomanFactoryInterface) {
            $this->factory = $factory;
        }

        parent::__construct($config, $options);
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
            codecept_debug('Generated PhantomJS Command:');
            codecept_debug($command);
        }

        $descriptorSpec = [
            ['pipe', 'r'],
            ['file', $this->getLogDir().'phantomjs.output.txt', 'w'],
            ['file', $this->getLogDir().'phantomjs.errors.txt', 'a'],
        ];

        codecept_debug('Starting PhantomJS Server.');

        $this->server->startServer($command, $descriptorSpec);

        codecept_debug('Waiting for the PhantomJS server to be reachable.');

        if($this->server->waitUntilServerIsUp($this->config['port'])) {
            codecept_debug('PhantomJS server now accessible.');
        }
    }

    /**
     * @param \Codeception\Event\SuiteEvent $event
     */
    public function afterSuite(SuiteEvent $event): void
    {
        if($this->server->isRunning()) {
            codecept_debug('Stopping PhantomJS Server.');

            if($this->server->stopServer()) {
                codecept_debug('PhantomJS server stopped.');
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
            || in_array($event->getSuite()->getName(), $this->config['suites'], true));
    }
}
