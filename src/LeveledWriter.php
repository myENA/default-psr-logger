<?php namespace MyENA;

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

use Psr\Log\LogLevel;

class LeveledWriter implements LoggerWriterInterface
{
    /** @var array */
    private const LEVEL_MAP = [
        LogLevel::DEBUG => 0,
        LogLevel::INFO => 1,
        LogLevel::NOTICE => 2,
        LogLevel::WARNING => 3,
        LogLevel::ERROR => 4,
        LogLevel::CRITICAL => 5,
        LogLevel::ALERT => 6,
        LogLevel::EMERGENCY => 7,
    ];

    private const LEVEL_UNKNOWN = 'UNKNOWN';

    /** @var array */
    private const SLUG_LEVEL_BUFFERS = [
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

    /** @var resource */
    private $_stream;

    /** @var string */
    private string $_level;

    /** @var bool */
    private bool $_color;

    /**
     * @param string $level
     * @param string|resource $stream
     */
    public function __construct(string $level = LogLevel::INFO, $stream = 'php://stdout')
    {
        $this->_setLevel($level);
        $this->_setStream($stream);
    }

    /**
     * @param string|resource $stream
     * @return \MyENA\LeveledWriter
     */
    public function withStream($stream): LeveledWriter
    {
        $w = clone $this;
        $w->_setStream($stream);
        return $w;
    }

    /**
     * @param string $level
     * @return \MyENA\LeveledWriter
     */
    public function withLevel(string $level): LeveledWriter
    {
        $w = clone $this;
        $w->_setLevel($level);
        return $w;
    }

    /**
     * @return \MyENA\LeveledWriter
     */
    public function withColor(): LeveledWriter
    {
        $w = clone $this;
        $w->_color = true;
        return $w;
    }

    /**
     * @return \MyENA\LeveledWriter
     */
    public function withoutColor(): LeveledWriter
    {
        $w = clone $this;
        $w->_color = false;
        return $w;
    }

    public function write(LogMessage $message): void
    {
        if ($this->_color) {

        }
    }

    /**
     * @param string|resource $stream
     * @return void
     */
    private function _setStream($stream): void
    {
        if (is_string($stream)) {
            $src = $stream;
            $stream = fopen($src, 'a');
            if (false === $stream) {
                throw new \RuntimeException(sprintf('Could not open "%s"', $src));
            }
        }
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException(sprintf('$stream must be string or resource, "%s" seen', gettype($stream)));
        }

        $this->_stream = $stream;
    }

    /**
     * @param string $level
     * @return void
     */
    private function _setLevel(string $level): void
    {
        if (!isset(self::LEVEL_MAP[$level])) {
            $this->_level = self::LEVEL_UNKNOWN;
        } else {
            $this->_level = $level;
        }
    }
}