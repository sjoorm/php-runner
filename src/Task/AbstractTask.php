<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Task;

use AndreasWeber\Runner\Exception\InvalidArgumentException;
use AndreasWeber\Runner\Exception\LogicException;
use AndreasWeber\Runner\Payload\PayloadInterface;
use AndreasWeber\Runner\Task\Exception\FailException;
use AndreasWeber\Runner\Task\Exception\RetryException;
use AndreasWeber\Runner\Task\Exception\SkipException;
use AndreasWeber\Runner\Task\Retries\Retries;

abstract class AbstractTask implements TaskInterface
{
    /**
     * @var PayloadInterface The payload
     */
    private $payload;

    /**
     * @var Retries Retries handler
     */
    private $maxRetries;

    /**
     * @var bool Was successfully executed
     */
    private $successfullyExecuted;

    /**
     * @var bool Fail task when a exit code other than 0 is given
     */
    private $failOnError;

    /**
     * __construct()
     */
    public function __construct()
    {
        $this->successfullyExecuted = false;
        $this->setFailOnError(false);
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
     * Gets the payload.
     *
     * @return PayloadInterface
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Sets the max allowed retries.
     *
     * @param int|Retries $retries
     *
     * @return $this
     */
    public function setMaxRetries($retries)
    {
        switch (gettype($retries)) {
            case 'integer':
                $this->maxRetries = new Retries($retries);
                break;
            case 'object':
                $this->maxRetries = $retries;
                break;
            default:
                throw new InvalidArgumentException('Invalid type for max retries given.');
                break;
        }
    }

    /**
     * Gets the max allowed retries.
     *
     * @return Retries
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * Marks the task as executed.
     * Marker is triggered by task runner.
     *
     * @return $this
     */
    public function markAsSuccessfullyExecuted()
    {
        $this->successfullyExecuted = true;

        return $this;
    }

    /**
     * Returns boolean true, when task was executed.
     *
     * @return bool
     */
    public function isSuccessfullyExecuted()
    {
        return $this->successfullyExecuted;
    }

    /**
     * Set to true, when task should fail,
     * when a different exit code than 0 is given.
     *
     * @param bool $failOnError
     *
     * @return $this
     */
    public function setFailOnError($failOnError)
    {
        $this->failOnError = (bool)$failOnError;

        return $this;
    }

    /**
     * Returns boolean true, when task should fail,
     * when a different exit code than 0 is given.
     *
     * @return boolean
     */
    public function isFailOnError()
    {
        return $this->failOnError;
    }

    /**
     * Run one or more precondition checks, before a task is executed.
     * If return value is boolean false, execution of task will be skipped.
     *
     * @return bool
     */
    public function unless()
    {
        return true;
    }

    /**
     * Sets up the task, for example, open a network connection.
     * This method is called before the task is executed.
     *
     * @return null
     */
    public function setUp()
    {
    }

    /**
     * Tear down the task, for example, close a network connection.
     * This method is called after the task was executed.
     *
     * @return null
     */
    public function tearDown()
    {
    }

    /**
     * Method call marks task as skipped.
     *
     * @return null
     * @throws SkipException
     */
    protected function skip()
    {
        throw new SkipException();
    }

    /**
     * Method call marks task as failed.
     *
     * @return null
     * @throws FailException
     */
    protected function fail()
    {
        throw new FailException();
    }

    /**
     * Method call triggers a task retry.
     *
     * @return null
     * @throws RetryException
     */
    protected function retry()
    {
        throw new RetryException();
    }

    /**
     * Task cloning is not allowed.
     *
     * @return null
     * @throws LogicException
     */
    public function __clone()
    {
        throw new LogicException('Cloning a task is not allowed.');
    }
}
