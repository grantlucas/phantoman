# Phantoman

[![Latest Version](https://img.shields.io/packagist/v/site5/phantoman.svg?style=flat-square)](https://packagist.org/packages/site5/phantoman)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/site5/phantoman.svg?style=flat-square)](https://packagist.org/packages/site5/phantoman)


The [Codeception](http://codeception.com/) extension for automatically starting
and stopping [PhantomJS](http://phantomjs.org/) when running tests.

## Minimum Requirements

- Codeception 2.1.0
- PHP 5.4

## Installation using [Composer](https://getcomposer.org)

```bash
$ composer require site5/phantoman
```

Be sure to enable the extension in `codeception.yml` as shown in
[configuration](#configuration) below.

## Recommended Additional Packages

### PhantomJS Installer via Composer

It is highly recommended that you use the [PhantomJS
Installer](https://github.com/jakoch/phantomjs-installer) package which will
install PhantomJS locally to your project in `vendor/bin`. Please follow the
[installation
instructions](https://github.com/jakoch/phantomjs-installer#installation)
provided.

**Phantoman uses `vendor/bin/phantomjs` by default. If any other installation of
PhantomJS is used, please set the path as shown in the configuration below.**

## Configuration

By default Phantoman will use the path `vendor/bin/phantomjs` and port `4444`.

All enabling and configuration is done in `codeception.yml`.

### Enabling Phantoman with defaults

```yaml
extensions:
    enabled:
        - Codeception\Extension\Phantoman
```

### Enabling Phantoman with alternate settings

```yaml
extensions:
    enabled:
        - Codeception\Extension\Phantoman
    config:
        Codeception\Extension\Phantoman:
            path: '/usr/bin/phantomjs'
            port: 4445
            suites: ['acceptance']
```

### Available options

Options set in the Phantoman configuration are mapped to [PhantomJS CLI
options](http://phantomjs.org/api/command-line.html). The currently supported
options are listed below.

#### Basic

- `path: {path}`
    - Full path to the PhantomJS binary.
    - Default: `vendor/bin/phantomjs`
- `port: {port}`
    - Webdriver port to start PhantomJS with.
    - Default: `4444`
- `debug: {true|false}`
    - Display debug output while Phantoman runs
    - Default: `false`

#### Proxy Support

- `proxy: {address:port}`
    - Sets the proxy server.
- `proxyType: {[http|socks5|none]}`
    - Specifies the proxy type.
- `proxyAuth: {username:password}`
    - Provides authentication information for the proxy.

#### Other

- `suites: {array|string}`
    - If omitted, PhantomJS is started for all suites.
    - Specify an array of suites or a single suite name.
        - If you're using an environment (`--env`), Codeception appends the
          environment name to the suite name in brackets. You need to include
          each suite/environment combination separately in the array.
            - `suites: ['acceptance', 'acceptance (staging)', 'acceptance (prod)']`
- `webSecurity: {true|false}`
    - Enables web security
- `ignoreSslErrors: {true|false}`
    - Ignores errors in the SSL validation.
    - Defaults to `false`
- `sslProtocol: {sslv3|sslv2|tlsv1|any}`
    - Sets the SSL protocol for secure connections
    - Defaults to `sslv3`
- `sslCertificatesPath: {path}`
    - Sets the location for custom CA certificates (if none set, uses system
      default).
- `remoteDebuggerPort: {port}`
    - Starts PhantomJS in a debug harness and listens on the specified port
- `remoteDebuggerAutorun: {true|false}`
    - Runs the script in the debugger immediately
    - Defaults to `false`
- `cookiesFile: {file path}`
    - Sets the file name to store the persistent cookies
- `diskCache: {true|false}`
    - Enabled disk cache
    - Defaults to `false`
- `maxDiskCacheSize: {number}`
    - Limit the size of the disk cache in KB
- `loadImages: {true|false}`
    - Loads all inlined images
    - Defaults to `true`
- `localStoragePath: {file path}`
    - The path to save LocalStorage content and WebSQL content
- `localStorageQuota: {number}`
    - Maximum size to allow for data in local storage in KB
- `localToRemoteUrlAccess: {true|false}`
    - Allows local content to access remote URL
    - Defaults to `false`
- `outputEncoding: {encoding}`
    - Sets the encoding for the terminal output
    - Default is `utf8`
- `scriptEncoding: {encoding}`
    - Sets the encoding used for starting the script
    - Default is `utf8`
- `silent: {true|false}`
    - Suppresses messages about starting and stopping the server
    - Default is `false`

## Usage

Once installed and enabled, running your tests with `php codecept run` will
automatically start the PhantomJS server and wait for it to be accessible before
proceeding with the tests.

```bash
Starting PhantomJS Server
Waiting for the PhantomJS server to be reachable..
PhantomJS server now accessible
```

Once the tests are complete, PhantomJS will be shut down.

```bash
Stopping PhantomJS Server
```
