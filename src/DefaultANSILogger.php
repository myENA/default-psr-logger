<?php namespace MyENA;

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

use Bramus\Ansi\Ansi;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use Bramus\Ansi\Writers\StreamWriter;
use Psr\Log\LogLevel;

/**
 * Class DefaultANSILogger
 * @package MyENA
 */
class DefaultANSILogger extends DefaultLogger {

    /** @var \Bramus\Ansi\Ansi */
    protected Ansi $ansi;

    /** @var array */
    protected array $colorMap = [
        LogLevel::DEBUG => [SGR::COLOR_FG_WHITE],
        LogLevel::INFO => [SGR::COLOR_FG_GREEN],
        LogLevel::NOTICE => [SGR::COLOR_FG_CYAN],
        LogLevel::WARNING => [SGR::COLOR_FG_YELLOW],
        LogLevel::ERROR => [SGR::COLOR_FG_RED],
        LogLevel::CRITICAL => [SGR::COLOR_FG_RED],
        LogLevel::ALERT => [SGR::COLOR_FG_WHITE, SGR::COLOR_BG_RED_BRIGHT],
        LogLevel::EMERGENCY => [SGR::COLOR_FG_WHITE, SGR::COLOR_BG_RED_BRIGHT],
    ];

    /**
     * DefaultANSILogger constructor.
     * @param string $level
     * @param mixed $writeableStream
     */
    public function __construct(string $level = LogLevel::DEBUG, $writeableStream = null) {
        parent::__construct($level, $writeableStream);
        $this->ansi = new Ansi(new StreamWriter($this->stream));
    }

    /**
     * @param array $colorMap
     */
    public function setColorMap(array $colorMap): void {
        $this->colorMap = $colorMap;
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    protected function doLog(string $level, string $message, array $context = array()): void{

        if (!is_string($level) || !isset($this->levelMap[$level])) {
            throw new \InvalidArgumentException(sprintf('"%s" is an unknown log level', $level));
        }

        if ($this->levelMap[$this->level] <= $this->levelMap[$level]) {

            $slug = sprintf('[%s]%s', strtolower($level), $this->slugLevelBuffers[$level]);
            if ("\n" !== substr($message, -1)) {
                $message .= "\n";
            }

            $msg = sprintf('%s%s %s', $slug, date(static::$dateTimeFormat), $message);

            $this->ansi->color($this->colorMap[$level])->text($msg)->nostyle();
        }
    }
}