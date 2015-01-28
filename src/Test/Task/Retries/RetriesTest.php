<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Test\Task\Retries;

use AndreasWeber\Runner\Task\Retries\Retries;

class RetriesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Retries
     */
    private $retries;

    protected function setUp()
    {
        $this->retries = new Retries(1);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Max allowed retries count can't be negative or zero.
     */
    public function testSettingNegativeMaxRetriesFails()
    {
        new Retries(-1);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Max allowed retries count can't be negative or zero.
     */
    public function testSettingZeroMaxRetriesFails()
    {
        new Retries(0);
    }

    public function testIncreaseRetryCounter()
    {
        $this->retries->increase();

        $this->assertEquals(
            1,
            $this->retries->getRetries()
        );
    }

    /**
     * @expectedException \AndreasWeber\Runner\Task\Retries\Exception\MaxRetriesExceededException
     * @expectedExceptionMessage Max allowed retries exceeded. Allowed: 1. Tried: 2.
     */
    public function testThrowsExceptionWhenRetriesLimitIsReached()
    {
        $this->retries->increase();
        $this->retries->increase();
    }

    public function testResetRetries()
    {
        $this->retries->increase();
        $this->retries->reset();

        $this->assertEquals(
            0,
            $this->retries->getRetries()
        );
    }

    public function testGetMaxRetries()
    {
        $this->assertEquals(
            1,
            $this->retries->getMaxRetries()
        );
    }
}
