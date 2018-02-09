<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Class DefaultStateTest
 */
class DefaultStateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return \MyENA\DefaultLogger
     */
    public function testCanConstructWithoutArguments()
    {
        $logger = new \MyENA\DefaultLogger();
        $this->assertInstanceOf('\\MyENA\\DefaultLogger', $logger);

        return $logger;
    }

    /**
     * @depends testCanConstructWithoutArguments
     * @param \MyENA\DefaultLogger $logger
     * @return \MyENA\DefaultLogger
     */
    public function testDefaultLevelIsDebug(\MyENA\DefaultLogger $logger)
    {
        $this->assertEquals(\Psr\Log\LogLevel::DEBUG, $logger->getLogLevel());

        $out = $this->outputAllLevels($logger);
        $this->assertCount(count($logger->getPossibleLogLevels()), $out);

        return $logger;
    }

    /**
     * @depends testDefaultLevelIsDebug
     * @param \MyENA\DefaultLogger $logger
     */
    public function testCanChangeLogLevel(\MyENA\DefaultLogger $logger)
    {
        $logger->setLogLevel(\Psr\Log\LogLevel::NOTICE);
        $this->assertEquals(\Psr\Log\LogLevel::NOTICE, $logger->getLogLevel());

        $out = $this->outputAllLevels($logger);
        $this->assertCount(6, $out);
    }

    /**
     * @depends testCanConstructWithoutArguments
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionThrownWhenConstructingWithInvalidLogLevel()
    {
        new \MyENA\DefaultLogger('nope');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionThrownWhenSettingInvalidLogLevelAfterConstruction()
    {
        $logger = new \MyENA\DefaultLogger();
        $logger->setLogLevel('asdfasdfasdf');
    }

    /**
     * @param \MyENA\DefaultLogger $logger
     * @return array
     */
    protected function outputAllLevels(\MyENA\DefaultLogger $logger)
    {
        ob_start();
        foreach($logger->getPossibleLogLevels() as $name => $rank)
        {
            $logger->log($name, sprintf('This is a test of "%s" level logging', $name));
        }
        return array_filter(explode("\n", ob_get_clean()));
    }
}