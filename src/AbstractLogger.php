<?php namespace MyENA;

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

use Psr\Log\AbstractLogger as PSRAbstractLogger;
use Psr\Log\LogLevel;

abstract class AbstractLogger extends PSRAbstractLogger
{
    /** @var string */
    public static string $dateTimeFormat = \DateTime::RFC3339;

    /** @var resource */
    protected $stream;

    /** @var string */
    protected string $streamURI;
    /** @var string */
    protected string $streamMode;

    /** @var string */
    protected string $level;

    /** @var array */
    protected const LEVEL_MAP = [
        LogLevel::DEBUG => 0,
        LogLevel::INFO => 1,
        LogLevel::NOTICE => 2,
        LogLevel::WARNING => 3,
        LogLevel::ERROR => 4,
        LogLevel::CRITICAL => 5,
        LogLevel::ALERT => 6,
        LogLevel::EMERGENCY => 7,
    ];

    protected const LEVEL_UNKNOWN = 'UNKNOWN';

    /** @var array */
    protected const SLUG_LEVEL_BUFFERS = [
        LogLevel::DEBUG => '          ',
        LogLevel::INFO => '           ',
        LogLevel::NOTICE => '         ',
        LogLevel::WARNING => '        ',
        LogLevel::ERROR => '          ',
        LogLevel::CRITICAL => '       ',
        LogLevel::ALERT => '          ',
        LogLevel::EMERGENCY => '      ',
        self::LEVEL_UNKNOWN => '      ',
    ];

    /**
     * DefaultLogger constructor.
     *
     * @param string $level
     * @param resource|null $writeableStream
     */
    public function __construct(string $level = LogLevel::DEBUG, ?LoggerWriterInterface $writer = null)
    {
        $this->setLogLevel($level);

        if (null === $writeableStream){
            $this->stream = $this->defaultStream();
        } else {
            $this->stream = $writeableStream;
        }

        $m = stream_get_meta_data($this->stream);
        $this->streamMode = $m['mode'];
        $this->streamURI = $m['uri'];
    }

    /**
     * @return array
     */
    public function getPossibleLogLevels(): array
    {
        return self::SLUG_LEVEL_BUFFERS;
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->level;
    }

    /**
     * setLogLevel will limit what is written to a level greater than or equal to value passed in.
     *
     * @param string $level
     */
    public function setLogLevel(string $level): void
    {
        $cl = strtolower($level);
        if (!isset(self::LEVEL_MAP[$cl])) {
            throw new \InvalidArgumentException(sprintf(
                '%s - Log level must be one of the following values: ["%s"].  %s seen.',
                get_called_class(),
                implode('", "', array_keys(self::LEVEL_MAP)),
                $level
            ));
        }

        $this->level = $cl;
    }

    /**
     * defaultStream will be used if no alternative is passed in during construction or if specified stream closes
     * unexpectedly
     *
     * @return resource
     */
    protected function defaultStream()
    {
        return fopen('php://stdout', 'a');
    }

    protected function buildMessage(string $level, string $message, array $context): string {
        if (!isset(self::LEVEL_MAP[$level])) {
            throw new \InvalidArgumentException(sprintf('"%s" is an unknown log level', $level));
        }

        if (self::LEVEL_MAP[$this->level] <= self::LEVEL_MAP[$level]) {
            $slug = sprintf('[%s]%s', strtolower($level), self::SLUG_LEVEL_BUFFERS[$level]);
            if ("\n" !== substr($message, -1))
                $message .= "\n";

            $msg = sprintf('%s%s %s', $slug, date(static::$dateTimeFormat), $message);
        }
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function doLog(string $level, string $message, array $context): void
    {
        $level = strtolower($level);
        if (!isset(self::LEVEL_MAP[$level])) {
            $level = self::LEVEL_UNKNOWN;
        }
        if ($this->level > (self::LEVEL_MAP[$level] ?? PHP_INT_MAX)) {
            return
        }
        $lm = new LogMessage((self::LEVEL_MAP[$level] ?? self::LEVEL_UNKNOWN), $message, $context);

    }
}