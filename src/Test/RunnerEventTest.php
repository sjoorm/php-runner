<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Test\Event;

use AndreasWeber\Runner\Payload\ArrayPayload;
use AndreasWeber\Runner\Runner;
use AndreasWeber\Runner\Task\Collection;
use AndreasWeber\Runner\Task\Retries\Retries;
use AndreasWeber\Runner\Test\Stub\RunnerInvokedStub;
use AndreasWeber\Runner\Test\Task\Stub\ExitCodeOneStub;
use AndreasWeber\Runner\Test\Task\Stub\FailTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\RetryExceptionTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\RetryMethodTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\SetUpTearDownCalledTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\SkipTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\TaskStub;
use AndreasWeber\Runner\Test\Task\Stub\UnknownExceptionTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\UnlessFalseTaskStub;
use Monolog\Logger;

class RunnerEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Runner Task runner
     */
    private $runner;

    /**
     * @var ArrayPayload Payload
     */
    private $payload;

    /**
     * @var Collection Task collection
     */
    private $collection;

    protected function setUp()
    {
        $this->runner = new Runner();
        $this->payload = new ArrayPayload();

        $this->runner->setLogger(
            new Logger('testing')
        );

        $collection = new Collection();
        $collection->addTask(new TaskStub());

        $this->collection = $collection;

        $this->runner->setTaskCollection($this->collection);
    }

    /**
     * Runner-Level
     */

    public function testRunnerStartCallbackIsInvoked()
    {
        $mock = $this->getMock('AndreasWeber\Runner\Test\Stub\TaskInvokedStub');
        $mock->expects($this->once())->method('trigger')->willReturn(null);

        $this->runner->on('runner.start', array($mock, 'trigger'));
        $this->runner->run($this->payload);
    }

    public function testRunnerSuccessCallbackIsInvoked()
    {
        $mock = $this->getMock('AndreasWeber\Runner\Test\Stub\TaskInvokedStub');
        $mock->expects($this->once())->method('trigger')->willReturn(null);

        $this->runner->on('runner.success', array($mock, 'trigger'));
        $this->runner->run($this->payload);
    }

    public function testRunnerFailureCallbackIsInvoked()
    {
        $mock = $this->getMock('AndreasWeber\Runner\Test\Stub\TaskInvokedStub');
        $mock->expects($this->once())->method('trigger')->willReturn(null);

        $collection = new Collection();
        $collection->addTask(new UnknownExceptionTaskStub());
        $this->runner->setTaskCollection($collection);

        $this->runner->on('runner.failure', array($mock, 'trigger'));

        try {
            $this->runner->run($this->payload);
        } catch (\Exception $e) {
            // we only want the event to occur
        }
    }

    /**
     * Task-Level
     */

    public function testTaskExecutionCallbackIsInvoked()
    {
        $mock = $this->getMock('AndreasWeber\Runner\Test\Stub\TaskInvokedStub');
        $mock->expects($this->once())->method('trigger')->willReturn(null);

        $this->runner->on('runner.task.start', array($mock, 'trigger'));
        $this->runner->run($this->payload);
    }

    public function testTaskSuccessCallbackIsInvoked()
    {
        $mock = $this->getMock('AndreasWeber\Runner\Test\Stub\TaskInvokedStub');
        $mock->expects($this->once())->method('trigger')->willReturn(null);

        $this->runner->on('runner.task.success', array($mock, 'trigger'));
        $this->runner->run($this->payload);
    }

    public function testTaskRetryCallbackIsInvoked()
    {
        $mock = $this->getMock('AndreasWeber\Runner\Test\Stub\TaskInvokedStub');
        $mock->expects($this->once())->method('trigger')->willReturn(null);

        $task = new RetryMethodTaskStub(1);
        $task->setMaxRetries(new Retries(1));
        $collection = new Collection();
        $collection->addTask($task);
        $this->runner->setTaskCollection($collection);

        $this->runner->on('runner.task.retry', array($mock, 'trigger'));
        $this->runner->run($this->payload);
    }

    public function testTaskFailureCallbackIsInvokedByTaskFailure()
    {
        $mock = $this->getMock('AndreasWeber\Runner\Test\Stub\TaskInvokedStub');
        $mock->expects($this->once())->method('trigger')->willReturn(null);

        $task = new FailTaskStub();
        $collection = new Collection();
        $collection->addTask($task);
        $this->runner->setTaskCollection($collection);

        $this->runner->on('runner.task.failure', array($mock, 'trigger'));

        try {
            $this->runner->run($this->payload);
        } catch (\Exception $e) {
            // we only want the event to occur
        }
    }

    public function testTaskFailureCallbackIsInvokedByExitCodeNotZeroOrNull()
    {
        $mock = $this->getMock('AndreasWeber\Runner\Test\Stub\TaskInvokedStub');
        $mock->expects($this->once())->method('trigger')->willReturn(null);

        $task = new ExitCodeOneStub();
        $task->setFailOnError(true);
        $collection = new Collection();
        $collection->addTask($task);
        $this->runner->setTaskCollection($collection);

        $this->runner->on('runner.task.failure', array($mock, 'trigger'));

        try {
            $this->runner->run($this->payload);
        } catch (\Exception $e) {
            // we only want the event to occur
        }
    }

    public function testTaskSkipCallbackIsInvoked()
    {
        $mock = $this->getMock('AndreasWeber\Runner\Test\Stub\TaskInvokedStub');
        $mock->expects($this->once())->method('trigger')->willReturn(null);

        $task = new SkipTaskStub();
        $collection = new Collection();
        $collection->addTask($task);
        $this->runner->setTaskCollection($collection);

        $this->runner->on('runner.task.skip', array($mock, 'trigger'));

        try {
            $this->runner->run($this->payload);
        } catch (\Exception $e) {
            // we only want the event to occur
        }
    }
}
