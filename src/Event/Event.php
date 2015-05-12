<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Event;

use AndreasWeber\Runner\Payload\PayloadInterface;
use AndreasWeber\Runner\Runner;
use AndreasWeber\Runner\Task\TaskInterface;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends BaseEvent implements EventInterface
{
    /**
     * @var Runner
     */
    private $runner;

    /**
     * @var TaskInterface
     */
    private $task;

    /**
     * @var PayloadInterface
     */
    private $payload;

    /**
     * @var int Exit code
     */
    private $exitCode;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * Sets the runner.
     *
     * @param Runner $runner
     *
     * @return $this
     */
    public function setRunner(Runner $runner)
    {
        $this->runner = $runner;

        return $this;
    }

    /**
     * Returns the runner.
     *
     * @return Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * Sets the task.
     *
     * @param TaskInterface $task
     *
     * @return $this
     */
    public function setTask(TaskInterface $task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Returns the task
     *
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Sets the payload.
     *
     * @param PayloadInterface $payload
     *
     * @return $this
     */
    public function setPayload(PayloadInterface $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Returns the payload.
     *
     * @return PayloadInterface
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Sets the exit code.
     *
     * @param int $exitCode
     *
     * @return $this
     */
    public function setExitCode($exitCode)
    {
        \Assert\that($exitCode)->integer()->min(0);

        $this->exitCode = $exitCode;

        return $this;
    }

    /**
     * Returns the exit code.
     *
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Sets the exception.
     *
     * @param \Exception $exception
     *
     * @return $this
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * Returns the exception.
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
