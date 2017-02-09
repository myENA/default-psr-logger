<?php namespace MyENA;

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Class DefaultLogger
 *
 * @package MyENA
 */
class DefaultLogger extends AbstractLogger
{
    /** @var string */
    public static $dateTimeFormat = \DateTime::RFC3339;

    /** @var resource */
    protected $stream;

    /** @var string */
    protected $streamURI;
    /** @var string */
    protected $streamMode;

    /** @var string */
    protected $level;

    /** @var array */
    protected $levelMap = [
        LogLevel::DEBUG => 0,
        LogLevel::INFO => 1,
        LogLevel::NOTICE => 2,
        LogLevel::WARNING => 3,
        LogLevel::ERROR => 4,
        LogLevel::CRITICAL => 5,
        LogLevel::ALERT => 6,
        LogLevel::EMERGENCY => 7,
    ];

    /** @var array */
    protected $slugLevelBuffers = [
        LogLevel::DEBUG => '          ',
        LogLevel::INFO => '           ',
        LogLevel::NOTICE => '         ',
        LogLevel::WARNING => '        ',
        LogLevel::ERROR => '          ',
        LogLevel::CRITICAL => '       ',
        LogLevel::ALERT => '          ',
        LogLevel::EMERGENCY => '      ',
    ];

    /**
     * DefaultLogger constructor.
     *
     * @param string $level
     * @param resource|null $writeableStream
     */
    public function __construct($level = LogLevel::DEBUG, $writeableStream = null)
    {
        $this->setLogLevel($level);

        if (null === $writeableStream)
            $this->stream = $this->defaultStream();
        else
            $this->stream = $writeableStream;

        $m = stream_get_meta_data($this->stream);
        $this->streamMode = $m['mode'];
        $this->streamURI = $m['uri'];
    }

    /**
     * @return array
     */
    public function getPossibleLogLevels()
    {
        return $this->levelMap;
    }

    /**
     * @return string
     */
    public function getLogLevel()
    {
        return $this->level;
    }

    /**
     * setLogLevel will limit what is written to a level greater than or equal to value passed in.
     *
     * @param string $logLevel
     */
    public function setLogLevel($logLevel)
    {
        if (!is_string($logLevel) || '' === ($level = strtolower($logLevel)) || !isset($this->levelMap[$level]))
        {
            throw new \InvalidArgumentException(sprintf(
                '%s - Log level must be one of the following values: ["%s"].  %s seen.',
                get_called_class(),
                implode('", "', array_keys($this->levelMap)),
                is_string($logLevel) ? $logLevel : gettype($logLevel)
            ));
        }

        $this->level = $level;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        if (!is_string($level) || !isset($this->levelMap[$level]))
            throw new \InvalidArgumentException(sprintf('"%s" is an unknown log level', $level));

        if ($this->levelMap[$this->level] <= $this->levelMap[$level])
        {
            $slug = sprintf('[%s]%s', strtolower($level), $this->slugLevelBuffers[$level]);
            if ("\n" !== substr($message, -1))
                $message .= "\n";

            $msg = sprintf('%s%s %s', $slug, date(static::$dateTimeFormat), $message);

            $this->tryLog($msg);
        }
    }

    /**
     * defaultStream will be used if no alternative is passed in during construction or if specified stream closes
     * unexpectedly
     *
     * @return resource
     */
    protected function defaultStream()
    {
        return fopen('php://output', 'a');
    }

    /**
     * tryLog will attempt to write output to local stream.  If unable, will kick-off re-open attempt
     *
     * @param string $msg
     * @param int $tries
     */
    protected function tryLog($msg, $tries = 0)
    {
        if ((bool)@fwrite($this->stream, $msg))
            return;

        if (0 < $tries)
        {
            trigger_error(sprintf('%s - Unable to log message: "%s"', get_called_class(), $tries, $msg));
            return;
        }

        $this->attemptStreamRecovery();

        $this->tryLog($msg, ++$tries);
    }

    /**
     * Will attempt to re-open stream in the event that it was closed unexpectedly.  Will use default if unable to
     * re-open custom
     *
     * @see DefaultLogger::defaultStream()
     */
    protected function attemptStreamRecovery()
    {
        if ('resource' === gettype($this->stream))
        {
            @fflush($this->stream);
            @fclose($this->stream);
        }

        $this->stream = fopen($this->streamURI, $this->streamMode);
        if (false === $this->stream)
        {
            trigger_error(sprintf(
                '%s - Unable to write to "%s" and re-open attempt failed, will default to php-output',
                get_called_class(),
                $this->streamURI));

            $this->stream = $this->defaultStream();
        }
    }
}
