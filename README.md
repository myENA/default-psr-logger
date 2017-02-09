# default-psr-logger
A simple PSR-3 compliant logger

## Installation

This library is designed to be used with [Composer](https://getcomposer.org)

Require entry:

```json
{
    "myena/default-psr-logger": "@stable"
}
```

## Basic Usage

```php
$logger = new \MyENA\DefaultLogger();

$logger->debug('hello!');
```

## Defaults

The default level of this logger is `debug`

The default stream for this logger is `php://output`

## Custom Levels

You may specify custom levels one of two ways:

**Construction**:
```php
$logger = new \MyENA\DefaultLogger(\Psr\Log\LogLevel::INFO);
```

**Post-Construction**:
```php
$logger->setLogLevel(\Psr\Log\LogLevel::INFO);
```

If you attempt to specify a level not denoted by
[\Psr\Log\LogLevel](https://github.com/php-fig/log/blob/1.0.2/Psr/Log/LogLevel.php), an exception will be thrown.

## Custom Stream

If you wish for the log output to go to a file or some other resource writeable by the 
[fwrite](http://php.net/manual/en/function.fwrite.php) function, you may pass it in as the 2nd argument.

```php
$logger = new \MyENA\DefaultLogger(\Psr\Log\LogLevel::DEBUG, fopen('tmp/test.log', 'ab'));
```

If this file becomes un-writeable for some reason, it will attempt to reconstruct the internal resource.  If it is
unable, it will revert to using the stream returned by the [defaultStream()](./src/DefaultLogger.php#L133).

**NOTE**: No write-ability determination is done, if you pass in a read-only stream it will ultimately not work.
