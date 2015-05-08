<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Test;

use AndreasWeber\Runner\Payload\ArrayPayload;
use AndreasWeber\Runner\Runner;
use AndreasWeber\Runner\Task\Collection;
use AndreasWeber\Runner\Task\Retries\Retries;
use AndreasWeber\Runner\Task\TaskInterface;
use AndreasWeber\Runner\Test\Stub\InvokedRunnerStub;
use AndreasWeber\Runner\Test\Task\Stub\ExitCodeOneStub;
use AndreasWeber\Runner\Test\Task\Stub\FailTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\RetryExceptionTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\RetryMethodTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\SetUpTearDownCalledTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\SkipTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\UnknownExceptionTaskStub;
use AndreasWeber\Runner\Test\Task\Stub\TaskStub;
use AndreasWeber\Runner\Test\Task\Stub\UnlessFalseTaskStub;
use Monolog\Logger;

class RunnerTest extends \PHPUnit_Framework_TestCase
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


        $this->runner->setLogger(new Logger('testing'));


        $collection = new Collection();
        $collection->addTask(new TaskStub());

        $this->collection = $collection;
    }

    public function testSetTaskCollection()
    {
        $this->runner->setTaskCollection($this->collection);

        $this->assertEquals(
            $this->collection,
            $this->runner->getTaskCollection()
        );
    }

    public function testSetTaskCollectionByConstructor()
    {
        $runner = new Runner($this->collection);

        $this->assertEquals(
            $this->collection,
            $runner->getTaskCollection()
        );
    }

    /**
     * @expectedException \AndreasWeber\Runner\Exception\LogicException
     * @expectedExceptionMessage Can't get task collection. None set.
     */
    public function testThrowsExceptionWhenNoTaskCollectionIsSet()
    {
        $this->runner->run($this->payload);
    }

    /**
     * @expectedException \AndreasWeber\Runner\Exception\LogicException
     * @expectedExceptionMessage Can't invoke task run. Empty task collection set.
     */
    public function testThrowsExceptionWhenEmptyTaskCollectionIsSet()
    {
        $this->runner->setTaskCollection(new Collection());
        $this->runner->run($this->payload);
    }

    public function testSuccessCallbackIsInvoked()
    {
        $this->runner->setTaskCollection($this->collection);

        $count = 0;
        $this->runner->onSuccess(
            function () use (&$count) {
                $count++;
            }
        );

        $this->runner->run($this->payload);

        $this->assertSame(1, $count, 'Callback not invoked');
    }

    public function testFailureCallbackIsInvoked()
    {
        $collection = new Collection();
        $collection->addTask(new UnknownExceptionTaskStub());

        $this->runner->setTaskCollection($collection);

        $count = 0;
        $this->runner->onFailure(
            function () use (&$count) {
                $count++;
            }
        );

        try {
            $this->runner->run($this->payload);
        } catch (\Exception $e) {
            // do nothing
        }

        $this->assertSame(1, $count, 'Callback not invoked');
    }

    public function testTaskExecutionCallbackIsInvoked()
    {
        $this->runner->setTaskCollection($this->collection);

        $count = 0;
        $this->runner->onTaskExecution(
            function () use (&$count) {
                $count++;
            }
        );

        $this->runner->run($this->payload);

        $this->assertSame(1, $count, 'Callback not invoked');
    }

    public function testTaskSuccessCallbackIsInvoked()
    {
        $this->runner->setTaskCollection($this->collection);

        $count = 0;
        $this->runner->onTaskSuccess(
            function () use (&$count) {
                $count++;
            }
        );

        $this->runner->run($this->payload);

        $this->assertSame(1, $count, 'Callback not invoked');
    }


    public function testTaskFailureCallbackIsInvokedByUnexpectedException()
    {
        $task = new UnknownExceptionTaskStub();
        $task->setFailOnError(true);

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);

        $count = 0;
        $this->runner->onTaskFailure(
            function () use (&$count) {
                $count++;
            }
        );

        try {
            $this->runner->run($this->payload);
        } catch (\Exception $e) {
            // do nothing
        }

        $this->assertSame(1, $count, 'Callback not invoked');
    }

    public function testTaskFailureCallbackIsInvokedByExitCodeNotZeroOrNull()
    {
        $task = new ExitCodeOneStub();
        $task->setFailOnError(true);

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);

        $count = 0;
        $this->runner->onTaskFailure(
            function () use (&$count) {
                $count++;
            }
        );

        try {
            $this->runner->run($this->payload);
        } catch (\Exception $e) {
            // do nothing
        }

        $this->assertSame(1, $count, 'Callback not invoked');
    }


    public function testSetUpIsCalled()
    {
        $task = new SetUpTearDownCalledTaskStub();

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);

        $this->assertTrue($task->isSetUpCalled());
    }

    public function testTearDownIsCalled()
    {
        $task = new SetUpTearDownCalledTaskStub();

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);

        $this->assertTrue($task->isTearDownCalled());
    }

    public function testSuccessfulRunMarksTaskAsSuccess()
    {
        $task = new TaskStub();

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);

        $this->assertTrue($task->isSuccessfullyExecuted());
    }

    public function testRetryATaskByMethodIsSuccessful()
    {
        $task = new RetryMethodTaskStub();
        $task->setMaxRetries(new Retries(3));

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);

        $this->assertSame(3, $task->getRunsCount());
    }

    public function testRetryATaskByExceptionIsSuccessful()
    {
        $task = new RetryExceptionTaskStub();
        $task->setMaxRetries(new Retries(3));

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);

        $this->assertSame(2, $task->getRunsCount());
    }

    /**
     * @expectedException \AndreasWeber\Runner\Runner\Exception\RunFailedException
     * @expectedExceptionMessage Max allowed retries exceeded. Allowed: 1. Tried: 2.
     */
    public function testExceedingMaxRetriesFailsRun()
    {
        $task = new RetryMethodTaskStub();
        $task->setMaxRetries(new Retries(1));

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);
    }

    /**
     * @expectedException \AndreasWeber\Runner\Runner\Exception\RunFailedException
     * @expectedExceptionMessage A retry exception was thrown, but no retries instance was set.
     */
    public function testCantThrowRetryExceptionWhenNoRetriesInstanceIsSet()
    {
        $task = new RetryMethodTaskStub();

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);
    }

    public function testSkippingTask()
    {
        $task = new SkipTaskStub();

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);

        $this->assertSame(
            0,
            $task->getRunsCount()
        );
    }

    /**
     * @expectedException \AndreasWeber\Runner\Runner\Exception\RunFailedException
     * @expectedExceptionMessage Complete run failed:
     */
    public function testFailingTask()
    {
        $task = new FailTaskStub();

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);
    }

    public function testRunReturnsPayload()
    {
        $this->runner->setTaskCollection($this->collection);
        $payload = $this->runner->run($this->payload);

        $this->assertSame(
            $this->payload,
            $payload
        );
    }

    public function testPayloadGetsInjectedInTask()
    {
        $task = new TaskStub();

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);

        $this->assertSame(
            $this->payload,
            $task->getPayload()
        );
    }

    public function testWhenUnlessIsFalseTaskWillBeSkipped()
    {
        $task = new UnlessFalseTaskStub();

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);

        $this->assertSame(
            0,
            $task->getRunsCount()
        );
    }

    public function testDetachingRunner()
    {
        $runner = $this->runner;
        $attachedRunner = new InvokedRunnerStub();

        $runner->setTaskCollection($this->collection);
        $runner->attach($attachedRunner);
        $runner->detach($attachedRunner);

        $runner->run($this->payload);

        $this->assertFalse($attachedRunner->isInvoked());
    }

    public function testAttachedRunnerGetsInvokedWhenPreviousRunWasSuccessful()
    {
        $runner = $this->runner;

        $attachedRunner = new InvokedRunnerStub();
        $attachedRunner->setTaskCollection(
            new Collection(
                array(
                    new TaskStub()
                )
            )
        );

        $runner->setTaskCollection($this->collection);
        $runner->attach($attachedRunner);

        $runner->run($this->payload);

        $this->assertTrue($attachedRunner->isInvoked());
    }

    public function testAttachedRunnerGetsNotInvokedWhenPreviousRunFailed()
    {
        $runner = $this->runner;

        $attachedRunner = new InvokedRunnerStub();
        $runner->attach($attachedRunner);

        $collection = new Collection();
        $collection->addTask(new FailTaskStub());

        $runner->setTaskCollection($collection);

        try {
            $runner->run($this->payload);
        } catch (\Exception $e) {
            // do nothing
        }

        $this->assertFalse($attachedRunner->isInvoked());
    }

    /**
     * @expectedException \AndreasWeber\Runner\Exception\LogicException
     * @expectedExceptionMessage Can't attach already attached runner.
     */
    public function testAttachingAlreadyAttachedRunnerFails()
    {
        $runner = new InvokedRunnerStub();

        $this->runner->attach($runner);
        $this->runner->attach($runner);
    }

    /**
     * @expectedException \AndreasWeber\Runner\Exception\LogicException
     * @expectedExceptionMessage Can't detach not attached runner.
     */
    public function testDetachingNotAttachedRunnerFails()
    {
        $runner = new InvokedRunnerStub();

        $this->runner->detach($runner);
    }

    /**
     * @expectedException \AndreasWeber\Runner\Runner\Exception\RunFailedException
     * @expectedExceptionMessage Complete run failed: Task: AndreasWeber\Runner\Test\Task\Stub\ExitCodeOneStub failed
     *                           with exit code 1
     */
    public function testWhenFailOnErrorTrueAndExitCodeNotEqualZeroTaskFails()
    {
        $task = new ExitCodeOneStub();
        $task->setFailOnError(true);

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);
    }

    public function testWhenFailOnErrorFalseAndExitCodeNotEqualZeroRunIsSuccessful()
    {
        $task = new ExitCodeOneStub();
        $task->setFailOnError(false);

        $collection = new Collection();
        $collection->addTask($task);

        $this->runner->setTaskCollection($collection);
        $this->runner->run($this->payload);
    }
}
