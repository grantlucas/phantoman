<?php declare(strict_types = 1);

namespace PhantomanTests\unit;

use Codeception\Extension\Configurator\Configurator;
use Codeception\Test\Unit;

class ConfiguratorTest extends Unit
{
    private const FILE_NAME = 'phantom';
    private const FILE_EXE_NAME = 'phantom.exe';

    protected function setUp(): void
    {
        parent::setUp();
        touch(static::FILE_NAME);
        touch(Configurator::DEFAULT_PATH);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unlink(static::FILE_NAME);
        unlink(Configurator::DEFAULT_PATH);
    }

    public function testConfigureExtensionWithParams(): void
    {
        $configurator = new Configurator('Linux');

        $input = [
            'path' => static::FILE_NAME,
            'debug' => true,
            'port' => 1234,
            'suites' => ['testing'],
        ];

        $result = $configurator->configureExtension($input);

        self::assertIsArray($result);
        self::assertSame($input, $result);
        self::assertSame($input['path'], $result['path']);
        self::assertSame($input['debug'], $result['debug']);
        self::assertSame($input['port'], $result['port']);
        self::assertSame($input['suites'], $result['suites']);
    }

    public function testConfigureExtensionWithoutParams(): void
    {
        $configurator = new Configurator('Linux');

        $input = [];

        $result = $configurator->configureExtension($input);

        self::assertIsArray($result);
        self::assertSame(Configurator::DEFAULT_PATH, $result['path']);
        self::assertSame(Configurator::DEFAULT_DEBUG, $result['debug']);
        self::assertSame(Configurator::DEFAULT_PORT, $result['port']);
        self::assertSame(Configurator::DEFAULT_SUITES, $result['suites']);
    }

    public function testConfigureExtensionWithPath(): void
    {
        $configurator = new Configurator('Linux');

        $input = [
            'path' => static::FILE_NAME,
        ];

        $result = $configurator->configureExtension($input);

        self::assertIsArray($result);
        self::assertSame($input['path'], $result['path']);
        self::assertSame(Configurator::DEFAULT_DEBUG, $result['debug']);
        self::assertSame(Configurator::DEFAULT_PORT, $result['port']);
        self::assertSame(Configurator::DEFAULT_SUITES, $result['suites']);
    }

    public function testConfigureExtensionWithWindowsPath(): void
    {
        touch(static::FILE_EXE_NAME);

        $configurator = new Configurator('WIN');

        $input = [
            'path' => static::FILE_NAME,
        ];

        $result = $configurator->configureExtension($input);

        self::assertIsArray($result);
        self::assertSame("{$input['path']}.exe", $result['path']);
        self::assertSame(Configurator::DEFAULT_DEBUG, $result['debug']);
        self::assertSame(Configurator::DEFAULT_PORT, $result['port']);
        self::assertSame(Configurator::DEFAULT_SUITES, $result['suites']);

        unlink(static::FILE_EXE_NAME);
    }

    public function testConfigureExtensionWithWindowsExePath(): void
    {
        touch(static::FILE_EXE_NAME);

        $configurator = new Configurator('WIN');

        $input = [
            'path' => static::FILE_EXE_NAME,
        ];

        $result = $configurator->configureExtension($input);

        self::assertIsArray($result);
        self::assertSame($input['path'], $result['path']);
        self::assertSame(Configurator::DEFAULT_DEBUG, $result['debug']);
        self::assertSame(Configurator::DEFAULT_PORT, $result['port']);
        self::assertSame(Configurator::DEFAULT_SUITES, $result['suites']);

        unlink(static::FILE_EXE_NAME);
    }

    public function testConfigureExtensionWithDebug(): void
    {
        $configurator = new Configurator('Linux');

        $input = [
            'debug' => true,
        ];

        $result = $configurator->configureExtension($input);

        self::assertIsArray($result);
        self::assertSame(Configurator::DEFAULT_PATH, $result['path']);
        self::assertSame($input['debug'], $result['debug']);
        self::assertSame(Configurator::DEFAULT_PORT, $result['port']);
        self::assertSame(Configurator::DEFAULT_SUITES, $result['suites']);
    }

    public function testConfigureExtensionWithPort(): void
    {
        $configurator = new Configurator('Linux');

        $input = [
            'port' => 1234,
        ];

        $result = $configurator->configureExtension($input);

        self::assertIsArray($result);
        self::assertSame(Configurator::DEFAULT_PATH, $result['path']);
        self::assertSame(Configurator::DEFAULT_DEBUG, $result['debug']);
        self::assertSame($input['port'], $result['port']);
        self::assertSame(Configurator::DEFAULT_SUITES, $result['suites']);
    }

    public function testConfigureExtensionWithSuites(): void
    {
        $configurator = new Configurator('Linux');

        $input = [
            'suites' => ['test'],
        ];

        $result = $configurator->configureExtension($input);

        self::assertIsArray($result);
        self::assertSame(Configurator::DEFAULT_PATH, $result['path']);
        self::assertSame(Configurator::DEFAULT_DEBUG, $result['debug']);
        self::assertSame(Configurator::DEFAULT_PORT, $result['port']);
        self::assertSame($input['suites'], $result['suites']);
    }

    public function testConfigureExtensionWithSuitesString(): void
    {
        $configurator = new Configurator('Linux');

        $input = [
            'suites' => 'test',
        ];

        $result = $configurator->configureExtension($input);

        self::assertIsArray($result);
        self::assertSame(Configurator::DEFAULT_PATH, $result['path']);
        self::assertSame(Configurator::DEFAULT_DEBUG, $result['debug']);
        self::assertSame(Configurator::DEFAULT_PORT, $result['port']);
        self::assertIsArray($result['suites']);
        self::assertNotSame($input['suites'], $result['suites']);
        self::assertSame([$input['suites']], $result['suites']);
    }
}
