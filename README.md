# Phantoman

[![Latest Version](https://img.shields.io/packagist/v/site5/phantoman.svg?style=flat-square)](https://packagist.org/packages/site5/phantoman)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/site5/phantoman.svg?style=flat-square)](https://packagist.org/packages/site5/phantoman)


The [Codeception](http://codeception.com/) extension for automatically starting
and stopping [PhantomJS](http://phantomjs.org/) when running tests.

## Minimum Requirements

- Codeception 1.6.4
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

**Note:** We've found that `1.9.7` is out of date and instead use `dev-master`
for the package version.

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

#### Proxy Support

- `proxy: {address:port}`
    - Sets the proxy server.
- `proxyType: {[http|socks5|none]}`
    - Specifies the proxy type.
- `proxyAuth: {username:password}`
    - Provides authentication information for the proxy.

#### Other

- `webSecurity: {true|false}`
    - Enables web security

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
