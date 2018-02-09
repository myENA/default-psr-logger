<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Class CustomStateTest
 */
class CustomStateTest extends \PHPUnit\Framework\TestCase
{
    const TMP_DIR = __DIR__.'/../tmp';
    const TMP_LOGFILE = self::TMP_DIR.'/test.log';

    /** @var resource */
    protected static $fh;

    public static function setUpBeforeClass()
    {
        if (!is_dir(self::TMP_DIR) || !is_writable(self::TMP_DIR))
            throw new \RuntimeException(sprintf('Unable to finish tests, "%s" must be a writable path', self::TMP_DIR));

        self::$fh = fopen(self::TMP_LOGFILE, 'ab');
    }

    public static function tearDownAfterClass()
    {
        @unlink(self::TMP_LOGFILE);
    }

    /**
     * @return \MyENA\DefaultLogger
     */
    public function testCanConstructWithCustomStream()
    {
        $logger = new \MyENA\DefaultLogger(\Psr\Log\LogLevel::DEBUG, self::$fh);
        $this->assertInstanceOf('\\MyENA\\DefaultLogger', $logger);

        $this->assertFileExists(self::TMP_LOGFILE);

        return $logger;
    }

    /**
     * @depends testCanConstructWithCustomStream
     * @param \MyENA\DefaultLogger $logger
     * @return \MyENA\DefaultLogger
     */
    public function testCanWriteToCustomStream(\MyENA\DefaultLogger $logger)
    {
        foreach($logger->getPossibleLogLevels() as $name => $rank)
        {
            $logger->log($name, sprintf('This is a test of "%s" level logging', $name));
        }

        $this->assertCount(count($logger->getPossibleLogLevels()), array_filter(file(self::TMP_LOGFILE)));

        return $logger;
    }

    /**
     * @depends testCanWriteToCustomStream
     * @param \MyENA\DefaultLogger $logger
     */
    public function testStreamRecoveryWhenClosed(\MyENA\DefaultLogger $logger)
    {
        fclose(self::$fh);
        $logger->debug('o noes!');
        $this->assertCount(count($logger->getPossibleLogLevels()) + 1, array_filter(file(self::TMP_LOGFILE)));
    }
}