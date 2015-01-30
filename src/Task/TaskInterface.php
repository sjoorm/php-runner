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

use AndreasWeber\Runner\Exception\LogicException;
use AndreasWeber\Runner\Payload\PayloadInterface;
use AndreasWeber\Runner\Task\Retries\Retries;

interface TaskInterface
{
    /**
     * Sets the payload.
     *
     * @param PayloadInterface $payload
     *
     * @return $this
     */
    public function setPayload(PayloadInterface $payload);

    /**
     * Gets the payload.
     *
     * @return PayloadInterface
     */
    public function getPayload();

    /**
     * Sets the max allowed retries.
     *
     * @param int|Retries $retries
     *
     * @return $this
     */
    public function setMaxRetries($retries);

    /**
     * Gets the max allowed retries.
     *
     * @return Retries
     */
    public function getMaxRetries();

    /**
     * Executes the task.
     *
     * @param PayloadInterface $payload
     *
     * @return null
     */
    public function run(PayloadInterface $payload);

    /**
     * Marks the task as executed.
     * Marker is triggered by task runner.
     *
     * @return $this
     */
    public function markAsSuccessfullyExecuted();

    /**
     * Returns boolean true, when task was executed.
     *
     * @return bool
     */
    public function isSuccessfullyExecuted();

    /**
     * Run one or more precondition checks, before a task is executed.
     * If return value is boolean false, execution of task will be skipped.
     *
     * @return bool
     */
    public function unless();

    /**
     * Sets up the task, for example, open a network connection.
     * This method is called before the task is executed.
     *
     * @return null
     */
    public function setUp();

    /**
     * Tear down the task, for example, close a network connection.
     * This method is called after the task was executed.
     *
     * @return null
     */
    public function tearDown();

    /**
     * Task cloning is not allowed.
     *
     * @return null
     * @throws LogicException
     */
    public function __clone();
}
