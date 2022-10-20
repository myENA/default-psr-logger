<?php namespace MyENA;

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */


if (PHP_VERSION_ID >= 80000) {
    /**
     * DefaultLogger class for version of PHP >= 8.0
     */
    class DefaultLogger extends AbstractLogger
    {
        /**
         * Logs with an arbitrary level.
         *
         * @param mixed $level
         * @param string|\Stringable $message
         * @param array $context
         *
         * @return void
         */
        public function log($level, string|\Stringable $message, array $context = []): void
        {
            $this->doLog($level, $message, $context);
        }
    }
} else {
    /**
     * DefaultLogger class for PHP 7.4.*
     */
    class DefaultLogger extends AbstractLogger
    {
        /**
         * Logs with an arbitrary level.
         *
         * @param mixed $level
         * @param string $message
         * @param array $context
         *
         * @return void
         */
        public function log($level, $message, array $context = [])
        {
            $this->doLog($level, $message, $context);
        }
    }
}

