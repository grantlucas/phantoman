<?php
declare(strict_types = 1);

namespace Codeception\Extension\CommandHandler\Mapper;


class CommandMapper implements CommandMapperInterface
{
    /**
     * @var string[]
     */
    private $mapping = [
        'port'                   => '--webdriver',
        'proxy'                  => '--proxy',
        'proxyType'              => '--proxy-type',
        'proxyAuth'              => '--proxy-auth',
        'webSecurity'            => '--web-security',
        'ignoreSslErrors'        => '--ignore-ssl-errors',
        'sslProtocol'            => '--ssl-protocol',
        'sslCertificatesPath'    => '--ssl-certificates-path',
        'remoteDebuggerPort'     => '--remote-debugger-port',
        'remoteDebuggerAutorun'  => '--remote-debugger-autorun',
        'cookiesFile'            => '--cookies-file',
        'diskCache'              => '--disk-cache',
        'maxDiskCacheSize'       => '--max-disk-cache-size',
        'loadImages'             => '--load-images',
        'localStoragePath'       => '--local-storage-path',
        'localStorageQuota'      => '--local-storage-quota',
        'localToRemoteUrlAccess' => '--local-to-remote-url-access',
        'outputEncoding'         => '--output-encoding',
        'scriptEncoding'         => '--script-encoding',
        'webdriverLoglevel'      => '--webdriver-loglevel',
        'webdriverLogfile'       => '--webdriver-logfile',
    ];

    /**
     * @inheritDoc
     */
    public function mapParamsToCommandLineArgs(array $config): string
    {
        $params = [];
        foreach($config as $configKey => $configValue) {
            if(!empty($this->mapping[$configKey])) {
                if(is_bool($configValue)) {
                    $configValue = $configValue ? 'true' : 'false';
                }
                $params[] = $this->mapping[$configKey].'='.$configValue;
            }
        }

        return implode(' ', $params);
    }
}
