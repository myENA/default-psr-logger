<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Class DefaultANSITest
 */
class DefaultANSITest extends \PHPUnit\Framework\TestCase {
    /**
     * @return \MyENA\DefaultANSILogger
     */
    public function testCanConstructWithoutArguments() {
        $logger = new \MyENA\DefaultANSILogger();
        $this->assertInstanceOf(\MyENA\DefaultANSILogger::class, $logger);
        return $logger;
    }

}