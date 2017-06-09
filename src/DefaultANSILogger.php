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
    protected $ansi;

    /** @var array */
    protected $colorMap = [
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
     * @param null $writeableStream
     */
    public function __construct($level = LogLevel::DEBUG, $writeableStream = null) {
        parent::__construct($level, $writeableStream);
        $this->ansi = new Ansi(new StreamWriter($this->stream));
    }

    /**
     * @param array $colorMap
     */
    public function setColorMap(array $colorMap) {
        $this->colorMap = $colorMap;
    }

    /**
     * @param string $msg
     * @param int $tries
     */
    protected function tryLog($msg, $tries = 0) {
        // TODO: Catch errors...
        $this->ansi->text($msg)->lf();
    }

    /**
     * Attempts to set the new, "recovered" writer to ansi
     */
    protected function attemptStreamRecovery() {
        parent::attemptStreamRecovery();
        $this->ansi->setWriter(new StreamWriter($this->stream));
    }
}