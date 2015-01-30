<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Test\Task;

use AndreasWeber\Runner\Payload\ArrayPayload;
use AndreasWeber\Runner\Task\Retries\Retries;
use AndreasWeber\Runner\Test\Task\Stub\TaskStub;

class AbstractTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskStub
     */
    private $task;

    /**
     * @var ArrayPayload
     */
    private $payload;

    protected function setUp()
    {
        $this->task = new TaskStub();
        $this->payload = $this->getMock('AndreasWeber\Runner\Payload\ArrayPayload');
    }

    public function testPayload()
    {
        $this->task->setPayload(
            $this->payload
        );

        $this->assertSame(
            $this->payload,
            $this->task->getPayload()
        );
    }

    public function testSetRetriesByInteger()
    {
        $this->task->setMaxRetries(3);
        $retries = $this->task->getMaxRetries();

        $this->assertInstanceOf('AndreasWeber\Runner\Task\Retries\Retries', $retries);
        $this->assertSame(3, $retries->getMaxRetries());
    }

    public function testSetRetriesByInstance()
    {
        $retries = new Retries(3);
        $this->task->setMaxRetries($retries);

        $this->assertInstanceOf('AndreasWeber\Runner\Task\Retries\Retries', $retries);
        $this->assertSame(3, $retries->getMaxRetries());
    }

    /**
     * @expectedException \AndreasWeber\Runner\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid type for max retries given.
     */
    public function testSetRetriesWithInvalidTypeFails()
    {
        $this->task->setMaxRetries('123');
    }

    public function testMarkAsSuccessfullyExecuted()
    {
        $this->task->markAsSuccessfullyExecuted();

        $this->assertTrue(
            $this->task->isSuccessfullyExecuted()
        );
    }

    public function testMarkAsSuccessfullyExecutedIsFalseByDefault()
    {
        $this->assertFalse(
            $this->task->isSuccessfullyExecuted()
        );
    }

    public function testGetRetriesIsNullByDefault()
    {
        $this->assertNull(
            $this->task->getMaxRetries()
        );
    }

    public function testGetPayloadIsNullByDefault()
    {
        $this->assertNull(
            $this->task->getPayload()
        );
    }

    public function testUnlessReturnsTrueByDefault()
    {
        $this->assertTrue(
            $this->task->unless()
        );
    }

    /**
     * @expectedException \AndreasWeber\Runner\Task\Exception\SkipException
     */
    public function testSkipThrowsSkipException()
    {
        $this->task->skip();
    }

    /**
     * @expectedException \AndreasWeber\Runner\Task\Exception\FailException
     */
    public function testFailThrowsFailException()
    {
        $this->task->fail();
    }

    /**
     * @expectedException \AndreasWeber\Runner\Task\Exception\RetryException
     */
    public function testRetryThrowsRetryException()
    {
        $this->task->retry();
    }

    /**
     * @expectedException \AndreasWeber\Runner\Exception\LogicException
     * @expectedExceptionMessage Cloning a task is not allowed.
     */
    public function testCloningTaskThrowsException()
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $cloned = clone $this->task;
    }
}
