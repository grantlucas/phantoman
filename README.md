# Phantoman

The [Codeception](http://codeception.com/) extension for automatically starting
and stopping [PhantomJS](http://phantomjs.org/) when running tests.

## Minimum Requirements

- Codeception 1.6.4
- PHP 5.4

## Installation

Phantoman is available via [Composer](https://getcomposer.org). Add the
following to the `require` section of your project's `composer.json` file.

```
"site5/phantoman": "1.*"
```

Then run `composer update` and `composer install` to complete the installation
process.

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

**Phantoman uses `vendor/bin` by default. If any other installation of PhantomJS
is used, please set the path as shown in the configuration below.**

## Configuration

Phantoman supports two configuration options, `path` and `port`. By default it
will look for PhantomJS to be installed in `vendor/bin` and it will use `4444`
for the web driver port as per Codeception's requirements.

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
            path: '{path to PhantomJS}'
            port: {port number}
            proxy: {address:port}
            proxyType: {[http|socks5|none]}
            proxyAuth: {username:password}
            webSecurity: {true|false}
```

## Usage

Once installed and enabled, running your tests with `php codecept run` will
automatically start the PhantomJS server and wait for it to be accessible before
proceeding with the tests.

```
Starting PhantomJS Server
Waiting for the PhantomJS server to be reachable..
PhantomJS server now accessible
```

Once the tests are complete, PhantomJS will be shut down.

```
Stopping PhantomJS Server
```
